<?php

namespace OpakeAdmin\Form\Charts;

use Opake\Form\AbstractForm;

class ChartGroupForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('name')->rule('filled')->error('You must specify name');
		$validator->field('document_ids')->rule('filled_callback', function($value) {
			return (!empty($value));
		})->error('You must select at least one chart');

	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'name',
			'document_ids'
		];
	}
}