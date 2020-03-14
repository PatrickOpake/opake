<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Helper\TimeFormat;

class FeeSchedule extends AbstractImport
{
	/**
	 * @var int
	 */
	protected $siteId;

	/**
	 * @var int
	 */
	protected $type;

	/**
	 * @var int
	 */
	protected $organizationId;

	/**
	 * @return int
	 */
	public function getSiteId()
	{
		return $this->siteId;
	}

	/**
	 * @param int $siteId
	 */
	public function setSiteId($siteId)
	{
		$this->siteId = $siteId;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $siteId
	 */
	public function setType($type)
	{
		$this->type = $type;
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

	public function load($filename)
	{

		if (!$this->organizationId || !$this->siteId) {
			throw new \Exception('Organization or Site is required');
		}

		$phpExcel = $this->readFromExcel($filename);

		$sheet = $phpExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$db = $this->pixie->db;
		$db->begin_transaction();
		try {

//			$cbsa = $sheet->getCell('B1')->getValue();
//			if ($cbsa) {
//				$cbsa = trim($cbsa);
//			}
//			$effectiveDate = $sheet->getCell('B2')->getValue();
//			$dateTime = null;
//			if ($effectiveDate) {
//				$effectiveDate = trim($effectiveDate);
//				$dateTime = \DateTime::createFromFormat('m/d/Y', $effectiveDate);
//				if (!$dateTime) {
//					throw new \Exception('Incorrect Effective Date format, use MM/DD/YYYY');
//				}
//			}
//
//			if ($cbsa || $dateTime) {
//				$info = $this->pixie->orm->get('Billing_FeeSchedule_Info')
//					->where('site_id', $this->siteId)
//					->find();
//
//				if (!$info->loaded()) {
//					$info->site_id = $this->siteId;
//					$info->organization_id = $this->organizationId;
//				}
//
//				if ($cbsa) {
//					$info->cbsa = $cbsa;
//				}
//
//				if ($dateTime) {
//					$info->effective_date = TimeFormat::formatToDB($dateTime);
//				}
//
//				$info->save();
//			}
			$this->pixie->orm->get('Billing_FeeSchedule_Record')
				->where('site_id', $this->siteId)
				->where('type', $this->type)
				->delete_all();

			$startRowNumber = 3;

			for ($i = $startRowNumber; $i <= $highestRow; ++$i) {
				$hcpcs = trim($sheet->getCell('A' . $i)->getValue());
				$desc = trim($sheet->getCell('B' . $i)->getValue());

				if (!$hcpcs) {
					throw new \Exception('HCPCS is empty at row ' . $i);
				}

				$amount = $this->parsePrice($sheet->getCell('C' . $i)->getValue(), $i);

				$model = $this->pixie->orm->get('Billing_FeeSchedule_Record');
				$model->organization_id = $this->organizationId;
				$model->site_id = $this->siteId;
				$model->type = $this->type;
				$model->hcpcs = $hcpcs;
				$model->description = $desc;
				$model->contracted_rate = $amount;

				$model->save();
			}

			$db->commit();
		} catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}

	}

	public static function getAllowedMimeTypes()
	{
		return [
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'text/csv',
		    'text/plain'
		];
	}

	protected function parsePrice($price, $rowNumber)
	{
		if ($price === null || $price === '') {
			return null;
		}

		if (is_float($price)) {
			return round($price, 2);
		}

		$price = trim($price);

		if (!preg_match('/^(\d+)(\.\d{1,2})?$/', $price)) {
			throw new \Exception('Invalid price format at row #' . $rowNumber . ': ' . $price);
		}

		return (float) $price;
	}
}