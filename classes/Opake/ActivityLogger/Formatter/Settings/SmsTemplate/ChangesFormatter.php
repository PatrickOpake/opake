<?php

namespace Opake\ActivityLogger\Formatter\Settings\SmsTemplate;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'poc_sms':
			case 'reminder_sms':
				return FormatterHelper::formatYesNo($value);

		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'reminder_sms' => 'Appointment Reminder SMS',
		    'hours_before' => 'Hours Before',
		    'schedule_msg' => 'Appointment Reminder SMS Template',
		    'poc_sms' => 'Point of Contact SMS',
		    'poc_msg' => 'Point of Contact SMS Template'
		];
	}
}