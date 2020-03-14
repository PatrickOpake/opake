<?php

namespace OpakeAdmin\Helper\Export;

use Opake\Helper\TimeFormat;

class UserCredentialsExport
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

	public function generateCsvForMedicalStaffs($credentials)
	{
		$tmpPath = $this->pixie->app_dir . '/_tmp/' . uniqid();
		$fh = fopen($tmpPath, 'w+');
		$columnLabels = $this->getColumnLabelsForMedicalStaffs();
		$firstRow = [];

		foreach ($columnLabels as $columnName) {
			$firstRow[] = (isset($columnLabels[$columnName])) ? $columnLabels[$columnName] : $columnName;
		}
		fputcsv($fh, $firstRow, $this->delimiter);

		foreach ($credentials as $credential) {
			try {
				if ($credential->loaded()) {

					$row = [];
					$row[] = $credential->user->last_name . ', ' . $credential->user->first_name;
					$row[] = $credential->npi_number;
					$row[] = $credential->tin;
					$row[] = $credential->taxonomy_code;
					$row[] = $credential->medical_licence_number;
					$row[] = $this->formatDate($credential->medical_licence_exp_date);
					$row[] = $credential->dea_number;
					$row[] = $this->formatDate($credential->dea_exp_date);
					$row[] = $credential->cds_number;
					$row[] = $this->formatDate($credential->cds_exp_date);
					$row[] = $credential->ecfmg;
					$row[] = $credential->insurance;
					$row[] = $this->formatDate($credential->insurance_exp_date);
					$row[] = $this->formatDate($credential->insurance_reappointment_date);
					$row[] = $this->formatDate($credential->acls_date);
					$row[] = $this->formatDate($credential->immunizations_ppp_due);
					$row[] = $this->formatDate($credential->immunizations_help_b);
					$row[] = $this->formatDate($credential->immunizations_rubella);
					$row[] = $this->formatDate($credential->immunizations_rubeola);
					$row[] = $this->formatDate($credential->immunizations_varicela);
					$row[] = $this->formatDate($credential->immunizations_mumps);
					$row[] = $this->formatDate($credential->immunizations_flue);
					$row[] = $this->formatDate($credential->retest_date);
					$row[] = $credential->upin;

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

	public function generateCsvForNonSurgicalStaffs($credentials)
	{
		$tmpPath = $this->pixie->app_dir . '/_tmp/' . uniqid();
		$fh = fopen($tmpPath, 'w+');
		$columnLabels = $this->getColumnLabelsForNonSurgicalStaffs();
		$firstRow = [];

		foreach ($columnLabels as $columnName) {
			$firstRow[] = (isset($columnLabels[$columnName])) ? $columnLabels[$columnName] : $columnName;
		}
		fputcsv($fh, $firstRow, $this->delimiter);

		foreach ($credentials as $credential) {
			try {
				if ($credential->loaded()) {

					$row = [];
					$row[] = $credential->user->last_name . ', ' . $credential->user->first_name;
					$row[] = $credential->licence_number;
					$row[] = $this->formatDate($credential->licence_expr_date);
					$row[] = $this->formatDate($credential->bls_date);
					$row[] = $this->formatDate($credential->acls_date);
					$row[] = $this->formatDate($credential->cnor_date);
					$row[] = $credential->malpractice;
					$row[] = $this->formatDate($credential->malpractice_exp_date);
					$row[] = $this->formatDate($credential->hp_exp_date);
					$row[] = $this->formatDate($credential->immunizations_ppp_due);
					$row[] = $this->formatDate($credential->immunizations_help_b);
					$row[] = $this->formatDate($credential->immunizations_rubella);
					$row[] = $this->formatDate($credential->immunizations_rubeola);
					$row[] = $this->formatDate($credential->immunizations_varicela);
					$row[] = $this->formatDate($credential->immunizations_mumps);
					$row[] = $this->formatDate($credential->immunizations_flue);

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
		return 'Users_credentials_export.csv';
	}

	/**
	 * @return array
	 */
	protected function getColumnLabelsForMedicalStaffs()
	{
		return [
			'Physician',
			'NPI #',
			'TIN',
			'Taxonomy Code',
			'Med License',
			'Exp Date',
			'DEA #',
			'Exp Date',
			'CDS',
			'Exp Date',
			'ECFMG',
			'Insurance',
			'Exp Date',
			'Reappointment',
			'ACLS',
			'PPD',
			'Hep B',
			'Rubella',
			'Rubeola',
			'Varicela',
			'Mumps',
			'Flu',
			'Retest Date',
			'UPIN'
		];
	}

	/**
	 * @return array
	 */
	protected function getColumnLabelsForNonSurgicalStaffs()
	{
		return [
			'Name',
			'Licence #',
			'Exp Date',
			'BLS',
			'ACLS',
			'CNOR',
			'Malpractice',
			'Exp Date',
			'H&P ',
			'PPD',
			'Hep B',
			'Rubella',
			'Rubeola',
			'Varicela',
			'Mumps',
			'Flu'
		];
	}
}