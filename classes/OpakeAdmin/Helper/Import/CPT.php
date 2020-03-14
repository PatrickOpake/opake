<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Helper\TimeFormat;
use PHPExcel_IOFactory;

class CPT extends AbstractImport
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
		$startRow = 2;
		$objPHPExcel = $this->readFromExcel($inputFile);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$db = $this->pixie->db;
		$db->begin_transaction();
		try {
			for ($i = $startRow; $i <= $highestRow; $i++) {
				$procedureName = $sheet->getCell('C' . $i)->getValue();
				$cptCode = $sheet->getCell('B' . $i)->getValue();
				$conceptId = $sheet->getCell('A' . $i)->getValue();

				if (!$procedureName && !$cptCode) {
					break;
				}

				if (!$procedureName) {
					$procedureName = 'N/A';
				}

				$cptModel = $this->pixie->orm->get('CPT')
					->where('code', $cptCode)
					->where('name', $procedureName)
					->where('concept_id', $conceptId)
					->order_by('id', 'desc')
					->limit(1)
					->find();

				if ($cptModel->loaded()) {
					$cptId = $cptModel->id;
				} else {
					$this->pixie->db->query('insert')
						->table('cpt')
						->data([
							'code' => $cptCode,
							'name' => $procedureName,
							'concept_id' => $conceptId
						])->execute();
					$cptId = $this->pixie->db->insert_id();
				}

				$this->pixie->db->query('insert')
					->table('cpt_to_cpt_year')
					->data([
						'cpt_id' => $cptId,
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