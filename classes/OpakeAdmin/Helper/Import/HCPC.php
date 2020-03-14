<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Helper\Currency;

class HCPC extends AbstractImport
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

	const START_ROW_DATA = 2;

	public function load($filename)
	{
		$objPHPExcel = $this->readFromExcel($filename);

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$this->pixie->db->begin_transaction();
		try {
			for ($i = self::START_ROW_DATA; $i <= $highestRow; $i++) {
				$code = $sheet->getCell('A' . $i)->getValue();
				$price = $sheet->getCell('F' . $i)->getValue();

				if ($code) {
					$model = $this->pixie->orm->get('HCPC')
						->where('code', $code)
						->find();
					$model->code = $code;
					$model->seqnum = $sheet->getCell('B' . $i)->getValue();
					$model->recid = $sheet->getCell('C' . $i)->getValue();
					$model->long_description = $sheet->getCell('D' . $i)->getValue();
					$model->short_description = $sheet->getCell('E' . $i)->getValue();
					$abu = $sheet->getCell('G' . $i)->getValue();
					if ($abu) {
						$model->abu = trim($abu);
					}
					if ($price) {
						$price = trim($price);
						$model->price = Currency::parseString($price);
					}

					$validator = $model->getValidator();
					if (!$validator->valid()) {
						$errors = $validator->errors();
						$fieldErrors = reset($errors);
						throw new \Exception(reset($fieldErrors) . ' (HCPC: ' . $code . ')');
					}

					$model->fire_events = false;
					$model->save();

					$this->pixie->db->query('insert')
						->table('hcpc_to_hcpc_year')
						->data([
							'hcpc_id' => $model->id(),
							'year_id' => $this->getYearId(),
							'active' => 1
						])->execute();
				}


			}

			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
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
