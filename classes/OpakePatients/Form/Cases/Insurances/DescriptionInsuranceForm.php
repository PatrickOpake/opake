<?php

namespace OpakePatients\Form\Cases\Insurances;

class DescriptionInsuranceForm extends AbstractInsuranceForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		/*
		$validator->field('description')->rule('filled')->error('You must specify Description');
		*/
	}

	protected function getFields()
	{
		return [
			'description'
		];
	}
}