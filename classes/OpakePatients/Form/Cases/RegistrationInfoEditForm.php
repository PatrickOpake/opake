<?php

namespace OpakePatients\Form\Cases;

use Opake\Form\AbstractForm;

class RegistrationInfoEditForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$formModel = $this->hasLoadedModel() ? $this->getModel() : null;

		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');
		$validator->field('ssn')->rule('filled')->error('You must specify Social Security #');
		$validator->field('ssn')->rule('numeric', $this)->error('The Social Security # field must be numeric');
		$validator->field('ssn')->rule('min_length', 9)->error('The Social Security # must be equal to 9 characters');
		$validator->field('ssn')->rule('max_length', 9)->error('The Social Security # must be equal to 9 characters');
		$validator->field('ssn')->rule('callback', function ($value) use ($formModel) {

			if ($formModel) {
				$patient = $this->pixie->orm->get('Patient')
					->where('ssn', $value)
					->where('id', '<>', $formModel->patient_id);
				$model = $patient->find();
				if ($model->loaded()) {
					return false;
				}
			} else {
				$patient = $this->pixie->orm->get('Patient')
					->where('ssn', $value);
				$model = $patient->find();
				if ($model->loaded()) {
					return false;
				}
			}

			return true;
		})->error(sprintf('Patient with SSN %s already exists', $this->getValueByName('ssn')));
		$validator->field('gender')->rule('filled')->error('You must specify Gender');
		$validator->field('race')->rule('filled')->error('You must specify Race');
		$validator->field('ethnicity')->rule('filled')->error('You must specify Ethnicity');
		$validator->field('language')->rule('filled')->error('You must specify Preferred Language');
		$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');
		$validator->field('dob')->rule('date')->error('Incorrect Date of Birth format');
		$validator->field('home_address')->rule('filled')->error('You must specify Home Address');

		$homeCountry = $this->getValueByName('home_country');
		if ($homeCountry && $homeCountry['id'] == 235) {
			$validator->field('home_city')->rule('filled')->error('You must specify Home City');
			$validator->field('home_zip_code')->rule('filled')->error('You must specify Home Zip code');
			$validator->field('home_state')->rule('filled')->error('You must specify Home State');
		} else {
			$validator->field('custom_home_city')->rule('filled')->error('You must specify Home City');
		}

		$validator->field('home_country')->rule('filled')->error('You must specify Home Country');
		$validator->field('home_phone')->rule('filled')->error('You must specify Phone #');
		$validator->field('home_phone')->rule('phone')->error('Incorrect Phone #');
		$validator->field('home_email')->rule('email')->error('Invalid Email Address');
		$validator->field('ec_name')->rule('filled')->error('You must specify Emergency Contact Name');
		$validator->field('ec_relationship')->rule('filled')->error('You must specify Relationship to Patient');
		$validator->field('ec_phone_number')->rule('filled')->error('You must specify Emergency Phone #');
		$validator->field('ec_phone_number')->rule('phone')->error('Incorrect Emergency Phone # format');
		$validator->field('employer_phone')->rule('phone')->error('Incorrect Employer Phone # format');
		$validator->field('additional_phone')->rule('phone')->error('Incorrect Additional Phone # format');
	}

	protected function getFields()
	{
		return [
			'title',
			'last_name',
			'first_name',
			'middle_name',
			'suffix',
			'gender',
			'dob',
			'home_address',
			'home_apt_number',
			'home_country',
			'home_state',
			'home_city',
			'custom_home_state',
			'custom_home_city',
			//'home_country_id',
			//'home_state_id',
			//'home_city_id',
			'home_zip_code',
			'home_email',
			'home_phone',
			'additional_phone',
			'ssn',
			'status_marital',
			'ethnicity',
			'race',
			'language',
			'status_employment',
			'employer',
			'employer_phone',
			'ec_name',
			'ec_phone_number',
			'ec_relationship'
		];
	}
}