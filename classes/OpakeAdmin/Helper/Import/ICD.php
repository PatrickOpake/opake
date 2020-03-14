<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Helper\TimeFormat;
use PHPExcel_IOFactory;

class ICD extends AbstractImport
{
	/**
	 * @var int
	 */
	protected $yearId;


	/**
	 * @return int
	 */
	public function getYearId()
	{
		return $this->yearId;
	}

	/**
	 * @param int $yearId
	 */
	public function setYearId($yearId)
	{
		$this->yearId = $yearId;
	}


	public function load($inputFile)
	{
		ini_set('memory_limit', '1024M');
		$startRow = 1;
		$objPHPExcel = $this->readFromExcel($inputFile);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$db = $this->pixie->db;
		$db->begin_transaction();
		try {
			for ($i = $startRow; $i <= $highestRow; $i++) {
				$name = $sheet->getCell('B' . $i)->getValue();
				$icdCode = $sheet->getCell('A' . $i)->getValue();

				if (!$name && !$icdCode) {
					break;
				}

				if (!$name) {
					$name = 'N/A';
				}

				$icdModel = $this->pixie->orm->get('ICD')->where('code', $icdCode)->where('desc', $name)->order_by('id', 'desc')->limit(1)->find();

				if ($icdModel->loaded()) {
					$icdId = $icdModel->id;
				} else {
					$this->pixie->db->query('insert')
						->table('icd')
						->data([
							'code' => $icdCode,
							'desc' => $name
						])->execute();
					$icdId = $this->pixie->db->insert_id();
				}
				$this->pixie->db->query('insert')
					->table('icd_to_icd_year')
					->data([
						'icd_id' => $icdId,
						'year_id' => $this->getYearId(),
						'active' => 1
					])->execute();
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
}