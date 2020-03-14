<?php

namespace Opake\Model\Cases\Coding;

use Opake\Model\AbstractModel;

class Value extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'case_coding_value';
	protected $_row = [
		'id' => null,
		'coding_id' => null,
		'value_code_id' => null,
		'amount' => null
	];
	protected $belongs_to = [
		'value_code' => [
			'model' => 'ValueCode',
			'key' => 'value_code_id'
		]
	];

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'coding_id' => $this->coding_id,
			'amount' => $this->amount ? number_format($this->amount, 2, '.', '') : null,
			'value_code' => $this->value_code->toArray()
		];
	}
}
