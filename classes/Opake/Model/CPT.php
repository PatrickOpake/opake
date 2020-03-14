<?php

namespace Opake\Model;

/**
 * Class CPT
 * @package Opake\Model
 * @deprecated
 */
class CPT extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'cpt';
	protected $_row = [
		'id' => null,
		'code' => '',
		'name' => '',
		'concept_id' => ''
	];

	protected $has_many = [
		'case_types' => [
			'model' => 'Cases_Type',
			'through' => 'case_type_cpt',
			'key' => 'cpt_id',
			'foreign_key' => 'case_type_id'
		],
		'cpt_years' => [
			'model' => 'CptYear',
			'through' => 'cpt_to_cpt_year',
			'key' => 'cpt_id',
			'foreign_key' => 'year_id'
		]
	];

	public function getValidator()
	{
		$validator = $this->pixie->validate->get($this->_row);
		$validator->field('name')->rule('filled')->rule('min_length', 2)->error('Invalid Name');
		return $validator;
	}

	public function getHCPC()
	{
		return $this->pixie->orm->get('HCPC')
			->where('code', $this->code)
			->find();
	}

	public function getCharge()
	{
		return $this->pixie->orm->get('Master_Charge')
			->where('cpt', $this->code)
			->find();
	}

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'code' => $this->code,
			'name' => $this->name,
			'concept_id' => $this->concept_id,
			'full_name' => $this->code . ' - ' . $this->name
		];
	}

	public function toArrayWithStatus()
	{
		$data = $this->toArray();
		$data['active'] = $this->active;

		return $data;
	}
}
