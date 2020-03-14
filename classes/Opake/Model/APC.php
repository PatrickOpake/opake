<?php

namespace Opake\Model;

class APC extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'apc';
	protected $_row = [
		'id' => null,
		'code' => '',
		'title' => '',
		'payment_rate' => null,
		'device_percent' => null,
		'device_amount' => null,
		'threshold_drug_percent' => null,
		'threshold_drug_amount' => null,
		'policy_drug_percent' => null,
		'policy_drug_amount' => null,
	];

}
