<?php

namespace OpakeAdmin\Form\Cases;

class FullForm extends ScheduleForm
{

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		parent::setValidationRules($validator);

		$validator->field('users')->rule('filled')->error('You must select at least one user for surgeon');
		$validator->field('type_id')->rule('filled')->error('You must specify procedure');
		$validator->field('description')->rule('max_length', 10000)->error('The Description must be less than or equal to 10000 characters');
	}

	protected function getFields()
	{
		return array_merge(parent::getFields(), [
			'users',
			'type_id',
			'description'
		]);
	}

}
