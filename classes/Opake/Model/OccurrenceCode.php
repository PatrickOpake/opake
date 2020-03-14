<?php

namespace Opake\Model;

class OccurrenceCode extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'occurrence_code';
	protected $_row = [
		'id' => null,
		'code' => '',
		'description' => ''
	];

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'code' => $this->code,
			'description' => $this->description,
			'full_name' => $this->code . ' - ' . $this->description
		];
	}
}
