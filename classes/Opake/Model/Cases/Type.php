<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class Type extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_type';
	protected $_row = array(
		'id' => null,
		'organization_id' => null,
		'cpt_id' => null,
		'code' => '',
		'name' => '',
		'length' => null,
		'active' => true,
		'is_2016' => false,
		'is_2017' => true,
		'archived' => 0,
		'last_update' => 0
	);

	protected $belongs_to = [
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
		'cpt' => [
			'model' => 'CPT',
			'key' => 'cpt_id'
		]
	];

	protected $has_many = [
		'cpt_codes' => [
			'model' => 'CPT',
			'through' => 'case_type_cpt',
			'key' => 'case_type_id',
			'foreign_key' => 'cpt_id'
		]
	];

	public function isHistorical()
	{
		if ($this->name) {
			$name = trim($this->name);
			if ($name === 'Historical Procedure') {
				return true;
			}
		}

		return false;
	}

	public function getValidator()
	{
		$validator = $this->pixie->validate->get($this->_row);
		$validator->field('name')->rule('filled')->error('You must specify Case Type name');
		$validator->field('name')->rule('unique', $this)->error(sprintf('Case Type with name %s already exists', $this->name));
		$validator->field('code')->rule('filled')->error('You must specify HCPCS/CPT code');
		$validator->field('code')->rule('unique', $this)->error('HCPCS/CPT code should be unique');

		return $validator;
	}

	public function getFullName()
	{
		$fullName = '';
		if ($this->code) {
			$fullName .= $this->code . ' - ';
		}
		$fullName .= $this->name;

		return $fullName;
	}

	public function getStatusStr()
	{
		if ($this->active) {
			return 'Active';
		}

		return 'Inactive';
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
				->where('organization_id', $this->organization_id)
				->where('cpt', $this->code)
				->find();
	}

	public function fromArray($data)
	{
		if (isset($data->length) && $data->length) {
			$data->length = strftime(\Opake\Helper\TimeFormat::TIME_FORMAT_DB, strtotime($data->length));
		} else {
			$data->length = null;
		}

		if (!empty($data->cpt)) {
			$data->cpt_id = $data->cpt->id;
		}

		return $data;
	}

	public function getCptCodes()
	{
		return $this->cpt_codes->find_all()->as_array();
	}

	public function getFirstCpt()
	{
		$cpt = $this->cpt_codes->limit(1)->find();
		if ($cpt->loaded()) {
			return $cpt->toArray();
		}

		return null;
	}

	public function toArray()
	{
		$cpt_codes = [];
		foreach ($this->getCptCodes() as $cpt_code) {
			$cpt_codes[] = $cpt_code->toArray();
		}

		return [
			'id' => (int)$this->id,
			'code' => $this->code,
			'name' => $this->name,
			'length' => $this->length,
			'full_name' => $this->getFullName(),
			'active' => (bool) $this->active,
			'is_2016' => (bool) $this->is_2016,
			'is_2017' => (bool) $this->is_2017,
			'cpt' => $this->cpt_id ? $this->cpt->toArray() : null
		];
	}
}
