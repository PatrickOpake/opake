<?php

namespace Opake\Model\Cases\Coding;

use Opake\Model\AbstractModel;

class Diagnosis extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_coding_diagnosis';
	protected $_row = [
		'id' => null,
		'coding_id' => null,
		'icd_id' => null,
		'row' => null
	];
	protected $belongs_to = [
		'coding' => [
			'model' => 'Cases_Coding',
			'key' => 'coding_id'
		],
		'icd' => [
			'model' => 'ICD',
			'key' => 'icd_id'
		]
	];
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Cases\Coding\DiagnosisFormatter'
	];

	public function get($property)
	{
		if (!$this->loaded()) {
			if ($property === 'icd') {
				return $this->pixie->orm->get('ICD', $this->icd_id);
			}
		}

		return null;
	}

}
