<?php

namespace OpakeAdmin\Form\Insurance\InsuranceTypes;

class AutoAccidentInsuranceForm extends AbstractInsuranceForm
{

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('insurance_company')->rule('filled')->error('You must specify Auto Insurance Company');

		/*
		$validator->field('adjuster_name')->rule('filled')->error('You must specify Auto Adjust Name');
		$validator->field('claim')->rule('filled')->error('You must specify Auto Claim #');
		$validator->field('adjuster_phone')->rule('filled')->error('You must specify Auto Adjuster Phone #');
		$validator->field('insurance_address')->rule('filled')->error('You must specify Auto Insurance Address');
		$validator->field('city')->rule('filled')->error('You must specify Auto Insurance City');
		$validator->field('state')->rule('filled')->error('You must specify Auto Insurance State');
		$validator->field('zip')->rule('filled')->error('You must specify Auto Insurance ZIP');
		$validator->field('accident_date')->rule('filled')->error('You must specify Accident Date');
		*/
	}

	protected function getFields()
	{
		return [
			'insurance_company',
			'insurance_name',
			'adjuster_name',
			'claim',
			'adjuster_phone',
			'insurance_address',
			'city',
			'state',
			'zip',
			'accident_date',
			'attorney_name',
			'attorney_phone',
			'cms1500_payer_id',
			'ub04_payer_id',
			'eligibility_payer_id',
			'address_insurance_selected',
		];
	}
}