<?php

namespace OpakeAdmin\Helper\Analytics\Reports;

use Opake\Helper\TimeFormat;

class InfectionReportGenerator
{

	const TYPE_INFECTION_REPORT = 6;
	const INFECTION_TYPE_IPC = 'ipc';
	const INFECTION_TYPE_POST_OP = 'post-op';

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var int
	 */
	protected $organizationId;

	/**
	 * @var \DateTime
	 */
	protected $dateFrom;

	/**
	 * @var \DateTime
	 */
	protected $dateTo;

	/**
	 * @var array
	 */
	protected $surgeons;

	/**
	 * @var string
	 */
	protected $infectionType;

	/**
	 * @var string
	 */
	protected $delimiter = ',';


	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @return int
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param int $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateFrom()
	{
		return $this->dateFrom;
	}

	/**
	 * @param \DateTime $dateFrom
	 */
	public function setDateFrom($dateFrom)
	{
		$this->dateFrom = $dateFrom;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTo()
	{
		return $this->dateTo;
	}

	/**
	 * @param \DateTime $dateTo
	 */
	public function setDateTo($dateTo)
	{
		$this->dateTo = $dateTo;
	}

	/**
	 * @return array
	 */
	public function getSurgeons()
	{
		return $this->surgeons;
	}

	/**
	 * @param array $surgeons
	 */
	public function setSurgeons($surgeons)
	{
		$this->surgeons = $surgeons;
	}

	/**
	 * @return string
	 */
	public function getInfectionType()
	{
		return $this->infectionType;
	}

	/**
	 * @param string $infectionType
	 */
	public function setInfectionType($infectionType)
	{
		$this->infectionType = $infectionType;
	}

	public function generate()
	{
		$excel = new \PHPExcel();
		$excel->getProperties()
			->setCreator('Opake')
			->setLastModifiedBy('Opake')
			->setTitle('Analytics_Report')
			->setSubject('Analytics_Report');

		$sheet = $excel->getSheet(0);
		$sheet->setTitle('Analytics Report');

		$headers = $this->formatHeaders();

		foreach ($headers as $index => $columnName) {
			$sheet->setCellValueByColumnAndRow($index, 1, $columnName);
		}

		$highestColumn = $sheet->getHighestColumn();
		for ($col = ord('a'); $col <= ord(strtolower($highestColumn)); $col++) {
			$sheet->getColumnDimension(chr($col))->setAutoSize(true);
		}

		$rowIndex = 2;
		foreach ($this->getCases() as $case) {
			try {
				if ($case->registration) {
					$data = $this->formatRow($case);
					foreach ($data as $index => $columnValue) {
						$sheet->setCellValueByColumnAndRow($index, $rowIndex, $columnValue);
					}
				}
				$rowIndex++;
			} catch (\Exception $e) {
				$this->pixie->logger->exception($e);
			}
		}

		$writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$tmpPath = tempnam(sys_get_temp_dir(), 'opk');
		$writer->save($tmpPath);

		if (is_file($tmpPath)) {
			/** @var \Opake\Model\UploadedFile $uploadedFile */
			$uploadedFile = $this->pixie->orm->get('UploadedFile');
			$uploadedFile->storeContent($this->getFileName(), file_get_contents($tmpPath), [
				'is_protected' => true,
				'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
			]);
			$uploadedFile->save();

			unlink($tmpPath);

			$model = $this->pixie->orm->get('Analytics_Reports_GeneratedReport');
			$model->file_id = $uploadedFile->id();
			$model->generateAccessKey();

			$model->save();

			return $model;
		}

		return null;
	}

	protected function getCases()
	{
		$model = $this->pixie->orm->get('Cases_Item');

		$query = $model->query;
		$query->fields('case.*');
		$query->where('organization_id', $this->organizationId);
		$query->group_by('case.id');

		if ($this->dateFrom) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', TimeFormat::formatToDB($this->dateFrom) . ' 00:00:00');
		}

		if ($this->dateTo) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', TimeFormat::formatToDB($this->dateTo) . ' 23:59:59');
		}

		if ($this->surgeons) {
			$query->join('case_user', ['case_user.case_id', 'case.id'], 'inner');
			$query->where('case_user.user_id', 'IN', $this->pixie->db->arr($this->surgeons));
		}

		if ($this->infectionType === self::INFECTION_TYPE_POST_OP) {
			$query->where('case.implants_flag', 1);
		}

		$query->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);
		$query->order_by('case.time_start', 'DESC');

		return $model->find_all();
	}

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @return array
	 */
	protected function formatRow($case)
	{
		$data = [];
		$data[] = $this->formatProcedureNames($case);
		$data[] = $this->formatSurgeons($case);
		$data[] = $this->formatDateTime($case->time_start);
		$data[] = $case->registration->patient->getFullMrn();
		$data[] = $case->registration->patient->first_name;
		$data[] = $case->registration->patient->last_name;
		$data[] = $this->formatDate($case->registration->patient->dob);

		if ($this->infectionType === self::INFECTION_TYPE_IPC) {
			$data = array_merge($data, ['Y / N', 'Y / N', 'Y / N']);
		} else if ($this->infectionType === self::INFECTION_TYPE_POST_OP) {
			$data = array_merge($data, ['Y / N']);
		}

		return $data;
	}

	protected function formatHeaders()
	{
		$headerRows = [
			'Procedure',
		    'Surgeon',
		    'Date of Service',
		    'MRN',
		    'Last Name',
		    'First Name',
		    'Date of Birth'
		];

		if ($this->infectionType === self::INFECTION_TYPE_IPC) {
			$headerRows = array_merge($headerRows, ['Infection', 'Hospitalization', 'Complications']);
		} else if ($this->infectionType === self::INFECTION_TYPE_POST_OP) {
			$headerRows = array_merge($headerRows, ['Infection']);
		}

		return $headerRows;
	}

	protected function formatProcedureNames($case)
	{
		if ($case->type->isHistorical()) {
			return $case->description;
		}

		$procedures = [];
		$procedures[] = $case->type->getFullName();

		$additionalCpts = $case->additional_cpts->find_all();
		foreach ($additionalCpts as $caseType) {
			if ($caseType->id() != $case->type->id()) {
				$procedures[] = $caseType->getFullName();
			}
		}

		return implode(', ', $procedures);
	}

	protected function formatSurgeons($case)
	{
		$names = [];

		foreach ($case->users->find_all() as $user) {
			$names[] = $user->getFullName();
		}

		return implode(', ', $names);
	}

	protected function formatDateTime($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDatetime($date);
		return TimeFormat::getDateTime($dateTime);
	}

	protected function formatDate($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDate($date);
		return TimeFormat::getDate($dateTime);
	}

	protected function getFileName()
	{
		$title = 'REPORT';
		if ($this->infectionType === self::INFECTION_TYPE_IPC) {
			$title = 'IPC';
		} else if ($this->infectionType === self::INFECTION_TYPE_POST_OP) {
			$title = 'POSTOP';
		}

		$nameParts[] = $title;

		if ($this->dateFrom) {
			$nameParts[] = $this->dateFrom->format('dMY');
		}

		if ($this->dateTo) {
			$nameParts[] = $this->dateTo->format('dMY');
		}

		return implode('_', $nameParts) . '.xlsx';
	}

}