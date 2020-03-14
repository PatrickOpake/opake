<?php

namespace OpakeAdmin\Helper\Import;

use Opake\Helper\TimeFormat;

class Procedures extends AbstractImport
{
	/**
	 * @var int
	 */
	protected $organizationId;

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

		if (!$this->organizationId) {
			throw new \Exception('Organization is required');
		}

		$phpExcel = $this->readFromExcel($filename);

		$sheet = $phpExcel->getActiveSheet();
		$highestRow = $sheet->getHighestRow();

		$caseTypesArray = [];
		$startRowNumber = 2;

		for ($i = $startRowNumber; $i <= $highestRow; ++$i) {
			$name = trim($sheet->getCell('A' . $i)->getValue());
			if (!$name) {
				throw new \Exception(sprintf('Row #%s: You must specify Case Type name', $i));
			}
			if ($name) {
				$code = trim($sheet->getCell('B' . $i)->getValue());
				$lengthHours = trim($sheet->getCell('C' . $i)->getValue());
				$lengthMinutes = trim($sheet->getCell('D' . $i)->getValue());
				$status = trim($sheet->getCell('E' . $i)->getValue());

				if (!$code) {
					throw new \Exception(sprintf('Row #%s: HCPCS/CPT code `%s` is empty', $i, $code));
				}
				if (!preg_match('/^[\w\-\.]{1,6}$/si', $code)) {
					throw new \Exception(sprintf('Row #%s: HCPCS/CPT code `%s` is in invalid format', $i, $code));
				}

				$codeQuery = $this->pixie->db->query('select')
					->table('cpt')
					->fields('id')
					->where('code', $code)
					->execute()
					->current();
				$cptId = $codeQuery ? $codeQuery->id : null;

				$length = new \DateTime();
				$lengthStr = null;
				if ($lengthHours || $lengthMinutes) {
					$length->setTime((int)$lengthHours, (int)$lengthMinutes);
					$lengthStr = TimeFormat::formatToDBDatetime($length);
				}

				if ($status == 'Active') {
					$active = 1;
				} else if ($status == 'Inactive') {
					$active = 0;
				} else {
					throw new \Exception('Status should be Active/Inactive');
				}

				$model = $this->pixie->orm->get('Cases_Type')
					->where(['code', $code], ['organization_id', $this->organizationId])
					->order_by('id', 'desc')
					->limit(1)
					->find();

				if ($model->loaded()) {
					$caseTypeId = $model->id;
				} else {
					$caseTypeId = null;
				}

				foreach ($caseTypesArray as $caseTypeItem) {
					if ($caseTypeItem['name'] == $name) {
//						throw new \Exception(sprintf('Case Type with name %s already exists', $name));
					}
					if ($caseTypeItem['code'] == $code) {
//						throw new \Exception(sprintf('Case Type with cpt code %s already exists', $cptCode));
					}
				}

				$caseTypesArray[] = [
					'id' => $caseTypeId,
					'cpt_id' => $cptId,
					'code' => $code,
					'name' => $name,
					'length' => $lengthStr,
					'active' => $active,
				];
			} else {
				throw new \Exception('Procedure name must be filled');
			}
		}

		$phpExcel = null;
		$sheet = null;

		$db = $this->pixie->db;
		$db->begin_transaction();
		try {
			$this->pixie->db->query('update')
				->table('case_type')
				->data(['last_update' => 0])
				->where(['organization_id', $this->organizationId])
				->execute();

			foreach ($caseTypesArray as $caseType) {
				if ($caseType['id']) {
					$this->pixie->db->query('update')
						->table('case_type')
						->data([
							'organization_id' => $this->organizationId,
							'cpt_id' => $caseType['cpt_id'],
							'code' => $caseType['code'],
							'name' => $caseType['name'],
							'length' => $caseType['length'],
							'active' => $caseType['active'],
							'is_2016' => 1,
							'is_2017' => 1,
							'last_update' => 1,
						    'archived' => 0
						])
						->where('id', $caseType['id'])->execute();
				} else {
					$this->pixie->db->query('insert')
						->table('case_type')
						->data([
							'organization_id' => $this->organizationId,
							'cpt_id' => $caseType['cpt_id'],
							'code' => $caseType['code'],
							'name' => $caseType['name'],
							'length' => $caseType['length'],
							'active' => $caseType['active'],
							'is_2016' => 1,
							'is_2017' => 1,
							'last_update' => 1,
						    'archived' => 0
						])->execute();
				}
			}

			$db->commit();

		} catch (\Exception $e) {
			$db->rollback();
			throw $e;
		}

		$this->pixie->db->query('update')
			->table('case_type')
			->data(['archived' => 1])
			->where(['organization_id', $this->organizationId], ['last_update', 0])
			->execute();

		$this->pixie->db->query('update')
			->table('case_type')
			->data(['last_update' => 0])
			->where(['organization_id', $this->organizationId])
			->execute();
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