<?php

namespace Opake\Formatter\SmsLog;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\SmsLog;

class SmsLogFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
				'fields' => [
					'phone_to',
				    'body',
				    'send_date',
				    'status_text',
				    'case_id',
				    'type'
				],
		        'fieldMethods' => [
					'send_date' => 'toDateTime',
		            'status_text' => 'statusText',
		            'case_id' => 'caseId',
		            'type' => 'type'
		        ]
			]);
	}

	protected function formatStatusText($name, $options, $model)
	{
		if ($model->status == SmsLog::STATUS_SENT) {
			return 'Sent';
		} else if ($model->status == SmsLog::STATUS_NOT_SENT) {
			return 'Not sent';
		}

		return '';
	}

	protected function formatCaseId($name, $options, $model)
	{
		return $model->case_log->case_id;
	}

	protected function formatType($name, $options, $model)
	{
		$type = $model->case_log->type;

		if ($type == \Opake\Model\Cases\SmsLog::TYPE_REMIND) {
			return 'Appointment Reminder SMS ';
		}

		if ($type == \Opake\Model\Cases\SmsLog::TYPE_POINT_OF_CONTACT) {
			return 'Point of Contact SMS';
		}

		return '';
	}
}