<?php

namespace OpakeAdmin\Form\Charts;

use Opake\Form\AbstractForm;

class ChartUploadedForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('name')->rule('filled')->error('You must specify name');
		$validator->field('name')->rule('callback', function($name) {
			return ChartRenameForm::isChartNameCorrect($name);
		})->error('Name contains incorrect characters');
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'name',
			'include_header'
		];
	}
}
