<?php

namespace OpakeAdmin\Helper\Chart\PDF;

use Opake\Helper\TimeFormat;

class DynamicFieldsWriter
{

	/**
	 * @var string
	 */
	protected $inputFilePath;

	/**
	 * @var string
	 */
	protected $outputFilePath;

	/**
	 * @var array
	 */
	protected $variables;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var bool
	 */
	protected $previewOnly = false;

	/**
	 * @param string $inputFilePath
	 * @param array $variables
	 */
	public function __construct($inputFilePath, $variables)
	{
		$this->inputFilePath = $inputFilePath;
		$this->variables = $variables;
	}

	/**
	 * @return \Opake\Model\Cases\Item
	 */
	public function getCase()
	{
		return $this->case;
	}

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function setCase($case)
	{
		$this->case = $case;
	}

	/**
	 * @return string
	 */
	public function getOutputFilePath()
	{
		return $this->outputFilePath;
	}

	/**
	 * @param string $outputFilePath
	 */
	public function setOutputFilePath($outputFilePath)
	{
		$this->outputFilePath = $outputFilePath;
	}

	/**
	 * @return mixed
	 */
	public function isPreviewOnly()
	{
		return $this->previewOnly;
	}

	/**
	 * @param mixed $previewOnly
	 */
	public function setPreviewOnly($previewOnly)
	{
		$this->previewOnly = $previewOnly;
	}

	public function writeFields()
	{
		if (!$this->isPreviewOnly() && !$this->case) {
			throw new \Exception('Case is required to write dynamic fields');
		}

		// initiate FPDI
		$pdf = new \FPDI();

		$font = 'Arial';
		$fontSize = 8;

		$lineHeight = 4;
		$cellWidth = 100;

		$blockMarginTop = 2;

		$pageCount = $pdf->setSourceFile($this->inputFilePath);

		for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

			$templateId = $pdf->importPage($pageNo);
			$size = $pdf->getTemplateSize($templateId);

			$pageWidth = $size['w'];
			$pageHeight = $size['h'];

			if ($pageWidth > $pageHeight) {
				$pdf->AddPage('L', [$pageWidth, $pageHeight]);
			} else {
				$pdf->AddPage('P', [$pageWidth, $pageHeight]);
			}

			$pdf->useTemplate($templateId);

			if (isset($this->variables[$pageNo])) {

				$allowedHeight = ($pageHeight - ($lineHeight));
				$allowedWidth = ($pageWidth - ($lineHeight));

				$pdf->SetFont($font, '', $fontSize);
				$pdf->SetXY(0, 0);
				$pdf->SetAutoPageBreak(false);
				foreach ($this->variables[$pageNo] as $field) {

					if (count($field) < 5) {
						throw new \Exception('Incorrect offset format');
					}

					$fieldName = $field[0];

					$offsetWidth = ($pageWidth / 100) * $field[1];
					$offsetHeight = ($pageHeight / 100) * $field[2];

					$blockWidth = ($pageWidth / 100) * $field[3];
					$blockHeight = ($pageHeight / 100) * $field[4];

					if ($offsetHeight > $allowedHeight) {
						$offsetHeight = $allowedHeight;
					}

					if ($offsetWidth > $allowedWidth) {
						$offsetWidth = $allowedWidth;
					}

					$pdf->SetXY($offsetWidth, $offsetHeight + $blockMarginTop);
					$pdf->MultiCell($blockWidth, $lineHeight, $this->formatVariable($fieldName), false, 'L');
				}
			}

		}

		$outputFile = ($this->outputFilePath) ? : $this->inputFilePath;

		$pdf->Output($outputFile, 'F');
	}

	protected function formatVariable($variableName)
	{
		if ($this->isPreviewOnly()) {
			$labels = $this->getVariableLabels();
			if (isset($labels[$variableName])) {
				return $labels[$variableName];
			}
			return 'Unknown Field';
		}

		$patient = $this->case->registration->patient;
		$case = $this->case;
		$site = $this->case->location->site;

		switch ($variableName) {

			case 'patient_first_name':
				return $patient->first_name;
			case 'patient_last_name':
				return $patient->last_name;
			case 'patient_full_name_first':
				$nameParts = [];
				$nameParts[] = $patient->first_name;
				if ($patient->middle_name) {
					$nameParts[] = $patient->middle_name;
				}
				$nameParts[] = $patient->last_name;
				$suffixesList = \Opake\Model\Patient::getSuffixesList();
				if (isset($suffixesList[$patient->suffix])) {
					$nameParts[] = $suffixesList[$patient->suffix];
				}
				return implode(' ', $nameParts);
			case 'patient_full_name_last':
				$nameParts = [];
				$nameParts[] = $patient->last_name;
				$suffixesList = \Opake\Model\Patient::getSuffixesList();
				if (isset($suffixesList[$patient->suffix])) {
					$nameParts[] = $suffixesList[$patient->suffix];
				}
				$nameParts[] = $patient->first_name;
				if ($patient->middle_name) {
					$nameParts[] = $patient->middle_name;
				}
				return implode(', ', $nameParts);
			case 'patient_account':
				return $this->case->id();
			case 'patient_age':
				return $patient->getAge();
			case 'patient_dob':
				return $patient->dob ? TimeFormat::fromDBDate($patient->dob)->format('m/d/Y') : '';
			case 'patient_gender':
				return $patient->getGender();
			case 'patient_pronoun':
				return $patient->getGenderTitle();
			case 'patient_address':
				return $patient->home_address;
			case 'patient_apt':
				return $patient->home_apt_number;
			case 'patient_city':
				return 	(($patient->custom_home_city) ? : (($patient->home_city->loaded()) ? $patient->home_city->name : ''));
			case 'patient_state':
				return (($patient->custom_home_state) ? : (($patient->home_state->loaded()) ? $patient->home_state->name : ''));
			case 'patient_country':
				return ($patient->home_country->loaded()) ? $patient->home_country->name : '';
			case 'patient_zip':
				return $patient->home_zip_code;
			case 'patient_mrn':
				return $patient->getFullMrn();
			case 'physician_name':
				return $case->getSurgeonNames();
			case 'dos':
				return $case->time_start ? TimeFormat::fromDBDatetime($case->time_start)->format('m/d/Y') : '';
			case 'primary_insurance':
				return $case->registration->getPrimaryInsuranceTitle();
			case 'site_name':
				return $site->name;
			case 'site_address':
				return $site->address;
			case 'site_city':
				return ($site->custom_city !== null) ? $site->custom_city :
					($site->city && $site->city->loaded()) ? $site->city->name : null;
			case 'site_state':
				return ($site->custom_state !== null) ? $site->custom_state :
					($site->state && $site->state->loaded()) ? $site->state->name : null;
			case 'site_country':
				return ($site->country && $site->country->loaded()) ? $site->country->name : '';
			case 'site_zip':
				return $site->zip_code;
			case 'site_phone':
				return $site->contact_phone;

		}

		return '';
	}

	protected function getVariableLabels()
	{
		return [
			'patient_first_name' => 'Patient First Name',
			'patient_last_name' => 'Patient Last Name',
			'patient_full_name_first' => 'Patient FullName First',
			'patient_full_name_last' => 'Patient FullName Last',
			'patient_account' => 'Patient Account #',
			'patient_age' => 'Patient Age',
			'patient_dob' => 'Patient Date of Birth',
			'patient_gender' => 'Patient Gender (Male/Female)',
			'patient_pronoun' => 'Patient Gender (He/She)',
			'patient_address' => 'Patient Street Address',
			'patient_apt' => 'Patient Apt #',
			'patient_city' => 'Patient City',
			'patient_state' => 'Patient State',
			'patient_country' => 'Patient Country',
			'patient_zip' => 'Patient ZIP',
			'patient_mrn' => 'Patient MRN',
			'physician_name' => 'Physician Name',
			'dos' => 'DOS',
			'primary_insurance' => 'Primary Insurance Co.',
			'site_name' => 'Site Name',
			'site_address' => 'Site Address',
			'site_city' => 'Site City',
			'site_state' => 'Site State',
			'site_country' => 'Site Country',
			'site_zip' => 'Site Zip',
			'site_phone' => 'Site Phone'
		];
	}

}