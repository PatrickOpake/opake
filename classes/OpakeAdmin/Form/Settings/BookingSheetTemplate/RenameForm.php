<?php

namespace OpakeAdmin\Form\Settings\BookingSheetTemplate;

use Opake\Form\AbstractForm;

class RenameForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('name')->rule('filled')->error('You must specify name');
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'name'
		];
	}
}