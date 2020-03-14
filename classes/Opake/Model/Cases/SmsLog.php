<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class SmsLog extends AbstractModel
{

	const TYPE_REMIND = 1;
	const TYPE_POINT_OF_CONTACT = 2;

	public $id_field = 'id';
	public $table = 'case_sms_log';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'sms_log_id' => null,
		'type' => null
	];

	protected $belongs_to = [
		'log' => [
			'model' => 'SmsLog',
			'key' => 'sms_log_id'
		]
	];

}
