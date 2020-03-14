<?php

namespace OpakePatients\Form\Cases\Insurances;

use Opake\Form\AbstractForm;

class WorkerCompInsuranceForm extends AbstractForm
{

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('insurance_name')->rule('filled')->error('You must specify Workers Comp Insurance Name');

		/*
		$validator->field('adjuster_name')->rule('filled')->error('You must specify Workers Comp Adjusters Name');
		$validator->field('claim')->rule('filled')->error('You must specify Workers Comp Claim #');
		$validator->field('adjuster_phone')->rule('filled')->error('You must specify Workers Comp Adjuster Phone #');
		$validator->field('insurance_address')->rule('filled')->error('You must specify Workers Comp Insurance Address');
		$validator->field('city')->rule('filled')->error('You must specify City');
		$validator->field('state')->rule('filled')->error('You must specify State');
		$validator->field('zip')->rule('filled')->error('You must specify ZIP');
		$validator->field('accident_date')->rule('filled')->error('You must specify Accident Date');
		*/
	}

	protected function getFields()
	{
		return [
			'insurance_name',
			'adjuster_name',
			'claim',
			'adjuster_phone',
			'insurance_company_phone',
			'authorization_number',
			'insurance_address',
			'city',
			'state',
			'zip',
			'accident_date',
			'employee_id',
			'employer_name',
			'employer_address',
			'employer_city_id',
			'employer_state_id',
			'employer_zip'
		];
	}

}