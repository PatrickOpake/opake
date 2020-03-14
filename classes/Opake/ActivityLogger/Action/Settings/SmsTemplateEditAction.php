<?php

namespace Opake\ActivityLogger\Action\Settings;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Comparer\SmsTemplateComparer;

class SmsTemplateEditAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'org' => $model->organization_id
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'reminder_sms',
			'hours_before',
			'schedule_msg',
			'poc_sms',
			'poc_msg'
		];
	}

	/**
	 * @return SmsTemplateComparer
	 */
	protected function createComparer()
	{
		return new SmsTemplateComparer();
	}
}