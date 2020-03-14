<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Helper\TimeFormat;

class ChargeMaster extends AbstractImport
{
	/**
	 * @var int
	 */
	protected $siteId;

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
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 600);

		if (!$this->organizationId || !$this->siteId) {
			throw new \Exception('Organization or Site is required');
		}

		$phpExcel = $this->readFromExcel($filename);

		$sheet = $phpExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$db = $this->pixie->db;
		$db->begin_transaction();
		try {

			$this->pixie->db->query('update')
				->table('master_charge')
				->data(['last_update' => 0])
				->where(['site_id', $this->siteId])
				->where('archived', 0)
				->execute();

			$startRowNumber = 4;

			$cptModifierCombinations = [];

			$firstItemUploaded = false;

			for ($i = $startRowNumber; $i <= $highestRow; ++$i) {
				$cdm = trim($sheet->getCell('A' . $i)->getValue());
				if ($cdm) {
					$desc = trim($sheet->getCell('B' . $i)->getValue());
					$amount = $this->parsePrice($sheet->getCell('C' . $i)->getValue(), $i);
					$revenueCode = trim($sheet->getCell('D' . $i)->getValue());
					$department = trim($sheet->getCell('E' . $i)->getValue());
					$cpt = trim($sheet->getCell('F' . $i)->getValue());
					$cptModifier1 = trim($sheet->getCell('G' . $i)->getValue());
					$cptModifier2 = trim($sheet->getCell('H' . $i)->getValue());
					$unitPrice = $this->parsePrice($sheet->getCell('I' . $i)->getValue(), $i);
					$ndc = trim($sheet->getCell('J' . $i)->getValue());
					$active = trim($sheet->getCell('K' . $i)->getValue());
					$generalLedger = trim($sheet->getCell('L' . $i)->getValue());
					$notes = trim($sheet->getCell('M' . $i)->getValue());
					$lastEditedDate = trim($sheet->getCell('N' . $i)->getValue());
					$historicalPrice = trim($sheet->getCell('O' . $i)->getValue());

					$existedEntry = $this->pixie->orm->get('Master_Charge')
						->where('cdm', $cdm)
						->where('site_id', $this->siteId)
						->where('archived', 0);

					if ($cpt) {
						$existedEntry->where('cpt', $cpt);
					}

					$model = $existedEntry->find();

					if (!$model->loaded()) {
						$model = $this->pixie->orm->get('Master_Charge');
						$model->organization_id = $this->organizationId;
						$model->site_id = $this->siteId;
					}

					$model->cdm = $cdm;
					$model->desc = $desc;
					$model->amount = $amount;
					$model->revenue_code = $revenueCode;
					$model->department = $department;
					$model->cpt = $cpt;
					$model->cpt_modifier1 = $cptModifier1;
					$model->cpt_modifier2 = $cptModifier2;
					$model->unit_price = $unitPrice;
					$model->ndc = $ndc;
					$model->active = $active;
					$model->general_ledger = $generalLedger;
					$model->notes = $notes;
					$model->last_edited_date = $lastEditedDate ? TimeFormat::formatToDBDatetime($lastEditedDate) : null;
					$model->historical_price = $historicalPrice;
					$model->last_update = 1;
					$model->archived = 0;

					$validator = $model->getValidator();
					if (!$validator->valid()) {
						$errors_text = '';
						foreach ($validator->errors() as $errors) {
							$errors_text .= implode('; ', $errors) . ";<br/>";
						}
						throw new \Exception(trim($errors_text, '; '));
					}

					foreach ($cptModifierCombinations as $combination) {
						if ($combination['cpt'] == $model->cpt) {
							if (
								(($combination['cpt_modifier1'] == $model->cpt_modifier1) && ($combination['cpt_modifier2'] == $model->cpt_modifier2))
								|| (($combination['cpt_modifier1'] == $model->cpt_modifier2) && ($combination['cpt_modifier2'] == $model->cpt_modifier1))
							) {
								throw new \Exception('Cpt and modifier combination could be used only once;');
							}
						}
					}

					try {
						$model->save();
						$firstItemUploaded = true;
					} catch (\Exception $e) {
						throw $e;
					}

					$cptModifierCombinations[] = [
						'cpt' => $model->cpt,
						'cpt_modifier1' => $model->cpt_modifier1,
						'cpt_modifier2' => $model->cpt_modifier2
					];

				} else {
					if (!$firstItemUploaded) {
						throw new \Exception('Charge code must be filled');
					}
				}

			}

			$this->pixie->db->query('update')
				->table('master_charge')
				->data(['archived' => 1])
				->where(['site_id', $this->siteId], ['last_update', 0])
				->execute();

			$this->pixie->db->query('update')
				->table('master_charge')
				->data(['last_update' => 0])
				->where(['site_id', $this->siteId])
				->execute();

		} catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}

		$db->commit();
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

		$price = trim($price);

		if (!preg_match('/^(\d+)(\.\d{1,2})?$/', $price)) {
			throw new \Exception('Invalid price format at row #' . $rowNumber . ': ' . $price);
		}

		return (float) $price;
	}
}