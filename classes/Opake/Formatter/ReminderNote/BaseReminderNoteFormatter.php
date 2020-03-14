<?php

namespace Opake\Formatter\ReminderNote;

use Opake\Formatter\BaseDataFormatter;

class BaseReminderNoteFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'user_id',
				'is_completed',
				'reminder_date',
				'note_type',
				'note_id',
			],
			'fieldMethods' => [
				'id' => 'int',
				'reminder_date' => 'toJsDate',
			]
		]);
	}

}
