<?php

namespace OpakeAdmin\Form\Insurance;

use Opake\Form\AbstractForm;
use Opake\Model\Cases\Registration;

class CoverageCheckingForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('type')->rule('filled')->error('You must specify Insurance Type');

		$validator->field('patient_first_name')->rule('filled')->error('You must specify Patient First Name');
		$validator->field('patient_last_name')->rule('filled')->error('You must specify Patient Last Name');
		if($this->getValueByName('relationship_to_insured') != Registration::RELATIONSHIP_TO_INSURED_SELF) {
			$validator->field('insured_first_name')->rule('filled')->error('You must specify Insured First Name');
			$validator->field('insured_last_name')->rule('filled')->error('You must specify Insured Last Name');
		}
		$validator->field('patient_dob')->rule('filled')->error('You must specify Patient Date of Birth');
		$validator->field('policy_num')->rule('filled')->error('You must specify Policy Number');
		$validator->field('payor_id')->rule('callback', function($value) {
			$payor = $this->pixie->orm->get('Insurance_Payor', $value);
			return ($payor->loaded() && $payor->navicure_eligibility_payor_id);
		})->error('This insurance company doesn\'t support Eligibility checking');
		$validator->field('organization_id')->rule('filled')->error('You must specify Organization');
		$validator->field('npi')->rule('min_length', 10)->error('Length of NPI should be equal to 10');
		$validator->field('npi')->rule('max_length', 10)->error('Length of NPI should be equal to 10');
		$validator->field('npi')->rule('callback', function ($value) {
			return (bool) preg_match('#^[0-9]*$#', $value);
		})->error('NPI must contain only numeric symbols');
	}

	protected function getFields()
	{
		return [
			'insured_first_name',
			'patient_first_name',
			'insured_last_name',
			'patient_last_name',
			'insured_dob',
			'patient_dob',
			'policy_num',
			'payor_id',
			'type',
			'organization_id',
			'relationship_to_insured',
			'insured_user_state',
			'patient_user_state',
			'npi',
			'insurance',
		];
	}
}