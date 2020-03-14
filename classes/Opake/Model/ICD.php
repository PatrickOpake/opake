<?php

namespace Opake\Model;

class ICD extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'icd';
	protected $_row = [
		'id' => null,
		'code' => '',
		'desc' => '',
		'is_2016' => false,
		'is_2017' => false
	];

	protected $has_many = [
		'icd_years' => [
			'model' => 'IcdYear',
			'through' => 'icd_to_icd_year',
			'key' => 'icd_id',
			'foreign_key' => 'year_id'
		]
	];

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'code' => $this->code,
			'desc' => $this->desc,
			'full_name' => $this->code . ' - ' . $this->desc,
			'is_2016' => $this->is_2016,
			'is_2017' => $this->is_2017
		];
	}


	public function toArrayWithStatus()
	{
		$data = $this->toArray();
		$data['active'] = $this->active;

		return $data;
	}

	public function checkCodesExistForYear($year)
	{
		$result = $this->pixie->db->query('select')
			->table('icd')
			->fields('icd.id')
			->join('icd_to_icd_year', ['icd_to_icd_year.icd_id', 'icd.id'])
			->join('icd_year', ['icd_year.id', 'icd_to_icd_year.year_id'])
			->where('icd_to_icd_year.active', 1)
			->where('icd_year.year', $year)
			->limit(1)
			->execute()
			->current();

		return ((bool) $result);
	}

	public function getLatestYearWithCodes()
	{
		$result = $this->pixie->db->query('select')
			->table('icd')
			->fields('icd_year.year')
			->join('icd_to_icd_year', ['icd_to_icd_year.icd_id', 'icd.id'])
			->join('icd_year', ['icd_year.id', 'icd_to_icd_year.year_id'])
			->where('icd_to_icd_year.active', 1)
			->order_by('icd_year.year', 'desc')
			->group_by('icd_year.year')
			->limit(1)
			->execute()
			->current();

		if ($result) {
			return $result->year;
		}

		return '2017';
	}
}
