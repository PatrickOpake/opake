<?php

namespace OpakePatients\Form\Cases\Insurances;

use Opake\Model\Insurance\AbstractType;

class RegularInsuranceForm extends AbstractInsuranceForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		if (!$this->insuranceModel->isInsuranceCompanyEqualsType()) {
		$validator->field('insurance')
			->rule('filled')
			->error('You must specify Insurance Company');
		}

		/*
		$validator->field('address_insurance')->rule('filled')->error('You must specify Address');
		$validator->field('relationship_to_insured')->rule('filled')->error('You must specify Relationship to Patient');

		$isNotSelfRelationship = $this->getValueByName('relationship_to_insured') != 0;

		if ($isNotSelfRelationship) {
			$validator->field('first_name')->rule('filled')->error('You must specify First Name');
			$validator->field('last_name')->rule('filled')->error('You must specify Last Name');
			$validator->field('gender')->rule('filled')->error('You must specify Gender');
			$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');

			$homeCountry = $this->getValueByName('country');
			if ($homeCountry && $homeCountry['id'] == 235) {
				$validator->field('city')->rule('filled')->error('You must specify Insurance City');
				$validator->field('zip_code')->rule('filled')->error('You must specify Insurance Zip code');
				$validator->field('state')->rule('filled')->error('You must specify Insurance State');
			} else {
				$validator->field('custom_city')->rule('filled')->error('You must specify Insurance City');
			}
			$validator->field('country')->rule('filled')->error('You must specify Home Country');

			$validator->field('phone')->rule('filled')->error('You must specify Phone #');
			$validator->field('phone')->rule('phone')->error('Incorrect Phone #');
			$validator->field('email')->rule('email')->error('Invalid Email Address');

			if ($this->insuranceModel->type == AbstractType::INSURANCE_TYPE_OTHER) {
				$validator->field('is_accident')->rule('filled')->error('You must specify Accident');
			}
		}

		$validator->field('policy_number')->rule('filled')->error('You must specify Policy #');
		$validator->field('group_number')->rule('filled')->error('You must specify Group #');
		*/
	}

	protected function getFields()
	{
		return [
			'insurance',
			'last_name',
			'first_name',
			'middle_name',
			'suffix',
			'dob',
			'gender',
			'ssn',
			'phone',
			'address',
			'address_insurance',
			'apt_number',
			'country',
			'state',
			'custom_state',
			'city',
			'custom_city',
			'zip_code',
			'relationship_to_insured',
			'type',
			'policy_number',
			'group_number',
			'employers_name',
			'is_primary',
			'oon_benefits',
			'pre_certification_required',
			'pre_certification_obtained',
			'self_funded',
			'co_pay',
			'co_insurance',
			'patients_responsibility',
			'individual_deductible',
			'individual_met_to_date',
			'family_deductible',
			'family_met_to_date',
			'yearly_maximum',
			'lifetime_maximum',
			'pre_certification_contact_name',
			'pre_certification',
			'oon_phone',
			'provider_phone',
			'authorization_or_referral_number',
			'is_accident'
		];
	}
}