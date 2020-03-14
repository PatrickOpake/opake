<?php

namespace OpakeAdmin\Form\Settings\Databases;

use Opake\Form\AbstractForm;

class UOMForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('name')->rule('filled')->error('You must specify name');
		$validator->field('name')->rule('unique', $this->getModel())->error('Unit with this name already exists');
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