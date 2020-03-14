<?php

namespace OpakeAdmin\Form;

use Opake\Form\AbstractForm;

class ReminderNoteForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{

	}

	protected function prepareValuesForModel($data)
	{
		$data['user_id'] = $this->pixie->auth->user()->id();
		if(isset($data['reminder_date'])) {
			$data['reminder_date'] = \Opake\Helper\TimeFormat::formatToDB($data['reminder_date']);
		}
		return $data;
	}

	protected function getFields()
	{
		return [
			'user_id',
			'is_completed',
			'reminder_date',
			'note_type',
			'note_id'
		];
	}
}