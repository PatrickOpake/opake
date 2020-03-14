<?php

namespace Opake\Model\Cases\Registration\Reconciliation;

use Opake\Model\AbstractModel;

class Allergy extends AbstractModel {

	public $id_field = 'id';
	public $table = 'reconciliation_allergy';
	protected $_row = [
		'id' => null,
		'reconciliation_id' => null,
		'name' => '',
		'description' => ''
	];

	protected $belongs_to = [
		'reconciliation' => [
			'model' => 'Cases_Registration_Reconciliation',
			'key' => 'reconciliation_id',
		]
	];

	public function toArray()
	{
		$data = parent::toArray();

		return $data;
	}

}
