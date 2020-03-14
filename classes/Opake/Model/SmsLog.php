<?php

namespace Opake\Model;

use Opake\Model\AbstractModel;

class SmsLog extends AbstractModel
{

	const STATUS_NOT_SENT = 0;
	const STATUS_SENT = 1;

	public $id_field = 'id';
	public $table = 'sms_log';
	protected $_row = [
		'id' => null,
		'message_sid' => null,
		'phone_to' => null,
		'body' => '',
		'status' => self::STATUS_NOT_SENT,
		'send_date' => null
	];

	protected $has_one = [
		'case_log' => [
			'model' => 'Cases_SmsLog',
			'key' => 'sms_log_id',
			'cascade_delete' => true
		],
	];

	protected $formatters = [
		'AnalyticsSmsLog' => [
			'class' => '\Opake\Formatter\SmsLog\SmsLogFormatter'
		]
	];

	/**
	 * @param string $body
	 * @param string $phoneTo
	 * @return string sid of new message
	 */
	public function send($body, $phoneTo, $code = null)
	{
		$this->phone_to = $phoneTo;
		$this->body = $body;
		$this->send_date = \Opake\Helper\TimeFormat::formatToDBDatetime(new \DateTime());

		$sid = \OpakeAdmin\Helper\SMS\Sender::getInstance()->send($body, $phoneTo, $code);

		$this->message_sid = $sid;
		$this->status = self::STATUS_SENT;
	}

}
