<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\TimeFormat;

class BillingCasesReportExport
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
						$row[] = $this->formatDateTimeToDate($case->time_start);
						$row[] = $case->registration->patient->last_name;
						$row[] = $case->registration->patient->first_name;
						$row[] = $case->registration->patient->getFormattedMrn();
						$row[] = $case->getFirstSurgeon()->getFullName();
					} else {
						$row[] = $this->formatDateToDate($report->dos);
						$row[] = $report->last_name;
						$row[] = $report->first_name;
						$row[] = $report->id_number;
						$row[] = $report->doctor;
					}
					$row[] = $report->insurance_modifiers;
					if ($report->case_id) {
						$row[] = $case->registration->getPrimaryInsuranceTitle();
					} else {
						$row[] = $report->insurance;
					}
					$row[] = $report->prefix;
					$row[] = $report->cd;
					$row[] = $report->cpt;
					$row[] = number_format($report->charges, 2, '.', '');
					$row[] = $this->formatDateToDate($report->recent_payment);
					$row[] = number_format($report->pmt, 2, '.', '');
					$row[] = number_format($report->ins_adj, 2, '.', '');
					$row[] = number_format($report->bs, 2, '.', '');
					$row[] = number_format($report->deductible, 2, '.', '');
					$row[] = number_format($report->co_pay, 2, '.', '');
					$row[] = number_format($report->tfr_prov, 2, '.', '');
					$row[] = number_format($report->balance, 2, '.', '');
					$row[] = number_format($report->var_cost, 2, '.', '');
					$row[] = number_format($report->or_time, 2, '.', '');
					$row[] = $report->notes;

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
            'DOS',
            'Last Name',
            'First Name',
            'ID #',
            'Dr',
            'Insur. Mod.',
            'Insurance',
            'Prefix',
            'CS',
            'CPT',
            'Charges',
            'Recent Payment',
            'Pmt',
            'Ins Adj',
            'BS',
            'Deductible',
            'Co-Pay',
            'Tfr/Prov',
            'Balance',
            'Var Cost',
            'OR Time',
            'Notes'
		];
	}
}