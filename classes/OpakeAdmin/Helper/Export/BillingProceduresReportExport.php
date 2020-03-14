<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\TimeFormat;

class BillingProceduresReportExport
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

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

	public function generateCsv($reports)
	{
		$tmpPath = $this->pixie->app_dir . '/_tmp/' . uniqid();
		$fh = fopen($tmpPath, 'w+');
		$columnLabels = $this->getColumnLabels();
		$firstRow = [];

		foreach ($columnLabels as $columnName) {
			$firstRow[] = (isset($columnLabels[$columnName])) ? $columnLabels[$columnName] : $columnName;
		}
		fputcsv($fh, $firstRow, $this->delimiter);

		foreach ($reports as $report) {
			try {
				if ($report->loaded()) {
					$case = $report->case;

					$row = [];
					if ($report->case_id) {
						$row[] = $case->registration->patient->getFormattedMrn();
						$row[] = $this->formatDateTimeToDate($case->time_start);
					} else {
						$row[] = $report->id_number;
						$row[] = $this->formatDateToDate($report->dos);
					}
					$row[] = $report->location;
					$row[] = $report->fee_type;
					$row[] = $report->cpt;
					$row[] = number_format($report->fee, 2, '.', '');
					if ($report->case_id) {
						$row[] = $case->registration->patient->last_name;
						$row[] = $case->registration->patient->first_name;
					} else {
						$row[] = $report->last_name;
						$row[] = $report->first_name;
					}
					$row[] = $report->pn1;
					$row[] = $report->dr_id;
					$row[] = $report->ins1;
					$row[] = $report->normalized_ins_id;
					$row[] = $report->insurance_name;

					fputcsv($fh, $row, $this->delimiter);
				}
			} catch (\Exception $e) {
				$this->pixie->logger->exception($e);
			}
		}

		fclose($fh);

		/** @var \Opake\Model\UploadedFile $uploadedFile */
		$uploadedFile = $this->pixie->orm->get('UploadedFile');
		$uploadedFile->storeContent($this->getFileName(), file_get_contents($tmpPath), [
			'is_protected' => true,
			'mime_type' => 'text/csv'
		]);
		$uploadedFile->save();

		unlink($tmpPath);

		return $uploadedFile;
	}

	protected function formatDateTimeToDate($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDatetime($date);
		return TimeFormat::getDateWithLeadingZeros($dateTime);
	}

	protected function formatDateToDate($date)
	{
		if (!$date) {
			return '';
		}

		$dateTime = TimeFormat::fromDBDate($date);
		return TimeFormat::getDateWithLeadingZeros($dateTime);
	}

	protected function getFileName()
	{
		return 'Billing_cases_report_export.csv';
	}

	/**
	 * @return array
	 */
	protected function getColumnLabels()
	{
		return [
			'ID #',
			'DOS',
			'Location',
			'Fee Type',
			'CPT',
			'Fee',
			'Last Name',
			'First Name',
			'Pn1',
			'DR ID',
			'Ins 1',
			'Normalized Ins ID',
			'Insurance Name '
		];
	}
}