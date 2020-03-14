<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\TimeFormat;

class CaseCancellationExport
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

	public function generateCsv($cancellations)
	{
		$tmpPath = $this->pixie->app_dir . '/_tmp/' . uniqid();
		$fh = fopen($tmpPath, 'w+');
		$columnLabels = $this->getColumnLabels();
		$firstRow = [];

		foreach ($columnLabels as $columnName) {
			$firstRow[] = (isset($columnLabels[$columnName])) ? $columnLabels[$columnName] : $columnName;
		}
		fputcsv($fh, $firstRow, $this->delimiter);

		foreach ($cancellations as $cancellation) {
			try {
				if ($cancellation->loaded()) {
					$case = $cancellation->case;
					$patient = $case->registration->patient;

					$row = [];
					$row[] = $patient->last_name . ', ' . $patient->first_name;
					$row[] = $patient->getFullMrn();
					$row[] = $case->getFirstSurgeonForDashboard();
					$row[] = $case->getFirstSurgeon()->practice_name;
					$row[] = $this->formatDateTimeToDate($cancellation->dos);
					$row[] = $this->formatDateTimeToDate($cancellation->cancel_time);
					$row[] = $this->formatCancelReason($cancellation);
					$row[] = $cancellation->canceled_user->getFullName();
					$row[] = $this->formatDateTimeToDate($cancellation->rescheduled_date);

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
		return TimeFormat::getDate($dateTime);
	}

	protected function formatCancelReason($cancellation)
	{
		$resultStr = '';
		$resultStr .= $cancellation->getCancellationStatus();
		if ($cancellation->cancel_reason) {
			$resultStr .= ' - ';
			$resultStr .= $cancellation->cancel_reason;
		}
		return $resultStr;
	}

	protected function getFileName()
	{
		return 'Cases_cancellations_export.csv';
	}

	/**
	 * @return array
	 */
	protected function getColumnLabels()
	{
		return [
			'Patient Name',
			'MRN',
			'Physician',
			'Practice',
			'DOS',
			'Date Canceled',
			'Reason',
			'Staff',
			'Rescheduled Date'
		];
	}
}