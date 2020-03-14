<?php

namespace Opake\Formatter\ReminderNote;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\ReminderNote;

class WidgetListFormatter extends BaseDataFormatter
{
	protected $note;

	protected function init()
	{
		parent::init();
		$this->note = $this->model->getNote();
	}

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'user_id',
				'acc_id',
				'is_completed',
				'reminder_date',
				'note_type',
				'note_id',
				'note_msg',
			],
			'fieldMethods' => [
				'id' => 'int',
				'reminder_date' => 'toJsDate',
				'acc_id' => 'accId',
				'note_msg' => 'noteMsg',
				'is_completed' => 'int',
			]
		]);
	}

	protected function formatNoteMsg($name, $options, $model)
	{
		return $this->note->text;
	}

	protected function formatAccId($name, $options, $model)
	{
		if($model->note_type == ReminderNote::TYPE_NOTE_BOOKING) {
			return (int)$this->note->booking_id;
		} else {
			return (int)$this->note->case_id;
		}
	}

}
