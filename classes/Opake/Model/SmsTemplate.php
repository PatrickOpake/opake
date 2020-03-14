<?php

namespace Opake\Model;

class SmsTemplate extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'sms_template';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'reminder_sms' => 0,
		'hours_before' => null,
		'schedule_msg' => '',
		'poc_sms' => 0,
		'poc_msg' => '',
		'acc_sid' => '',
		'auth_token' => ''
	];

	public function toArray()
	{
		$data = parent::toArray();
		$data['reminder_sms'] = (bool)$this->reminder_sms;
		$data['poc_sms'] = (bool)$this->poc_sms;
		return $data;
	}
}
