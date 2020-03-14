<?php

namespace OpakeAdmin\Helper\Printing\Utils\Chart;

class HeadersWriter
{
	const A4_WIDTH = 210;
	const A4_HEIGHT = 297;

	const LETTER_WIDTH = 215.9;
	const LETTER_HEIGHT = 279.4;

	/**
	 * @var string
	 */
	protected $inputFilePath;

	/**
	 * @var \Opake\Model\Organization
	 */
	protected $organization;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @param string $inputFile
	 */
	public function __construct($inputFile)
	{
		$this->inputFilePath = $inputFile;
	}

	/**
	 * @return \Opake\Model\Organization
	 */
	public function getOrganization()
	{
		return $this->organization;
	}

	/**
	 * @param \Opake\Model\Organization $organization
	 */
	public function setOrganization($organization)
	{
		$this->organization = $organization;
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
	 *
	 */
	public function writeHeaders()
	{
		if (!$this->organization) {
			throw new \Exception('Organization is required to write headers');
		}

		// initiate FPDI
		$pdf = new \FPDI();

		$font = 'Arial';
		$fontSize = 8;

		$marginLeft = 11;
		$marginTop = 8;

		$lineHeight = 4;
		$firstBlockMargin = 4;
		$secondAndThirdBlockTopMargin = 1.5;

		$targetHeight = self::LETTER_HEIGHT;
		$targetWidth = self::LETTER_WIDTH;

		$lines = [
			[
				$this->organization->name,
			    $this->removeNewLines($this->organization->address),
			    $this->formatPhone($this->organization->contact_phone)
			]
		];

		if ($this->case) {
			$lines[0][] = $this->formatSurgeonNames($this->case);
			$lines[] = [
				'Patient Name: ' . $this->case->registration->getFullNameWithMiddle(),
				'Date of Birth: ' . \Opake\Helper\TimeFormat::getDate($this->case->registration->dob),
				'Age/Sex: ' . $this->case->registration->getAge() . '/' . $this->case->registration->getGender()
			];

			$lines[] = [
				'Date of Service: ' . \Opake\Helper\TimeFormat::getDate($this->case->time_start),
				'MRN: ' . $this->case->registration->patient->getFullMrn(),
				'Account Number: ' . $this->case->id()

			];
		}

		$pageCount = $pdf->setSourceFile($this->inputFilePath);
		for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

			$templateId = $pdf->importPage($pageNo);
			$size = $pdf->getTemplateSize($templateId);

			$pageWidth = $size['w'];
			$pageHeight = $size['h'];

			if ($pageWidth > $pageHeight) {
				$pageWidth = $targetHeight;
				$pageHeight = $targetWidth;
				$pdf->AddPage('L', [$targetWidth, $targetHeight]);
			} else {
				$pageWidth = $targetWidth;
				$pageHeight = $targetHeight;
				$pdf->AddPage('P', [$targetWidth, $targetHeight]);
			}

			$pdf->useTemplate($templateId, null, null, $pageWidth, $pageHeight, false);
			$pdf->SetFont($font, '', $fontSize);
			$pdf->SetXY($marginLeft, $marginTop);
			$pdf->setFillColor(255, 255, 255);

			$widthWithoutMargins = $pageWidth - ($marginLeft * 2) - $firstBlockMargin;
			$blockSizes = [
				($widthWithoutMargins / 100) * 43,
				($widthWithoutMargins / 100) * 32,
				($widthWithoutMargins / 100) * 25
			];

			if (!isset($lines[1])) {
				$pdf->MultiCell($blockSizes[0] * 2, $lineHeight, implode("\n", $lines[0]), 0, 'L', false);
			} else {
				$pdf->MultiCell($blockSizes[0], $lineHeight, implode("\n", $lines[0]), 'R', 'L', false);
			}

			if (isset($lines[1])) {
				$pdf->SetXY($blockSizes[0] + $marginLeft + $firstBlockMargin, $marginTop + $secondAndThirdBlockTopMargin);
				$pdf->MultiCell($blockSizes[1], $lineHeight, implode("\n", $lines[1]), 0, 'L', false);
			}

			if (isset($lines[2])) {
				$pdf->SetXY(($blockSizes[0] + $blockSizes[1]) + $marginLeft + $firstBlockMargin, $marginTop + $secondAndThirdBlockTopMargin);
				$pdf->MultiCell($blockSizes[2], $lineHeight, implode("\n", $lines[2]), 0, 'L', false);
			}

		}

		$pdf->Output($this->inputFilePath, 'F');
	}

	protected function removeNewLines($text)
	{
		$text = str_replace("\n", ' ', $text);
		return $text;
	}

	protected function formatPhone($phone)
	{
		if (!$phone) {
			return '';
		}
		return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
	}

	protected function formatSurgeonNames($case)
	{
		$users = $case->getUsers();
		if ($users) {
			$names = [];
			foreach ($users as $user) {
				$names[] = $user->getFullName();
			}

			return 'Dr. ' . implode(', ', $names);
		}

		return '';
	}
}