<?php

namespace Opake\Model\Cases\Registration\Reconciliation;

use Opake\Model\AbstractModel;

class Medication extends AbstractModel {

	public $id_field = 'id';
	public $table = 'reconciliation_medication';
	protected $_row = [
		'id' => null,
		'reconciliation_id' => null,
		'name' => '',
		'dose' => '',
		'route' => '',
		'frequency' => '',
		'current' => null,
		'pre_op' => null,
		'post_op' => null,
		'rx' => null,
		'verify' => null,
		'resume' => null,
		'discontinue' => null,
		'comments' => ''
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
