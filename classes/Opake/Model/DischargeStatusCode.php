<?php

namespace Opake\Model;

class DischargeStatusCode extends AbstractModel
{
	const
		DEFAULT_ID = 1;

	public $id_field = 'id';
	public $table = 'discharge_status_code';
	protected $_row = [
		'id' => null,
		'code' => '',
		'effective_date' => null,
		'change_date' => null,
		'delete_date' => null,
		'verbiage' => ''
	];

	public function toArray()
	{
		return $this->loaded() ? [
			'id' => (int) $this->id,
			'code' => $this->code,
			'verbiage' => $this->verbiage,
			'full_name' => $this->code . ' - ' . $this->verbiage
		] : null;
	}

	/**
	 * @return \PHPixie\ORM\Model
	 */
	public function getDefaultValue()
	{
		return $this->where('id', self::DEFAULT_ID)->find();
	}
}
