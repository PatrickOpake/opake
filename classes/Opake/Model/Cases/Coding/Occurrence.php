<?php

namespace Opake\Model\Cases\Coding;

use Opake\Model\AbstractModel;

class Occurrence extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_coding_occurrence';
	protected $_row = [
		'id' => null,
		'coding_id' => null,
		'occurrence_code_id' => null,
		'date' => null
	];
	protected $belongs_to = [
		'occurrence_code' => [
			'model' => 'OccurrenceCode',
			'key' => 'occurrence_code_id'
		]
	];

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'coding_id' => $this->coding_id,
			'date' => $this->date,
			'occurrence_code' => $this->occurrence_code->toArray()
		];
	}

	public function get($property)
	{
		if (!$this->loaded()) {
			if ($property === 'occurrence_code' && $this->occurrence_code_id) {
				return $this->pixie->orm->get('OccurrenceCode', $this->occurrence_code_id);
			}
		}

		return null;
	}
}
