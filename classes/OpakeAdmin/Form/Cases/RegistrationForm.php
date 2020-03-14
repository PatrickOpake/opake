<?php

namespace OpakeAdmin\Form\Cases;

use Opake\Model\Cases\Item as CaseItem;
use Opake\Form\AbstractForm;

class RegistrationForm extends AbstractForm
{

	protected function prepareValues($data)
	{
		$result = parent::prepareValues($data);
		if (isset($result['home_country']) && $result['home_country']) {
			$result['home_country_id'] = $result['home_country']->id;
		}
		if (isset($result['home_city']) && $result['home_city']) {
			$result['home_city_id'] = $result['home_city']->id;
		}
		if (isset($result['home_state']) && $result['home_state']) {
			$result['home_state_id'] = $result['home_state']->id;
		}
		return $result;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$regModel = $this->getModel();
		$patientId = $this->getValueByName('patient_id');

		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');
		$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');
		$validator->field('admitting_diagnosis')->rule('filled')->error('You must specify Primary diagnosis');
		$validator->field('dob')->rule('date')->error('Incorrect Date of Birth format');

		/*$validator->field('ssn')->rule('numeric', $this)->error('The Social Security # field must be numeric');
		$validator->field('ssn')->rule('min_length', 9)->error('The Social Security # must be equal to 9 characters');
		$validator->field('ssn')->rule('max_length', 9)->error('The Social Security # must be equal to 9 characters');*/
		/*$validator->field('ssn')->rule('callback', function ($value) use($patientId, $regModel) {
			$patient_valid = true;
			$patient = $this->pixie->orm->get('Patient')
				->where('ssn', $value)
				->where('id', '<>', $patientId);
			if (isset($regModel->organization_id)) {
				$patient->where('organization_id', $regModel->organization_id);
			}
			$model = $patient->find();
			if ($model->loaded()) {
				$patient_valid = false;
			}
			return $patient_valid;
		})->error(sprintf('Patient with SSN %s already exists', $regModel->ssn));*/

		/*$validator->field('home_phone')->rule('phone')->error('Incorrect Phone #');
		$validator->field('home_email')->rule('email')->error('Invalid Email Address');
		$validator->field('ec_phone_number')->rule('phone')->error('Incorrect Emergency Phone # format');
		$validator->field('employer_phone')->rule('phone')->error('Incorrect Employer Phone # format');
		$validator->field('additional_phone')->rule('phone')->error('Incorrect Additional Phone # format');
		$validator->field('point_of_contact_phone')->rule('phone')->error('Incorrect Point of Contact Phone # format');*/
	}

	protected function getFields()
	{
		return [
			'title',
			'first_name',
			'middle_name',
			'last_name',
			'suffix',
			'ssn',
			'dob',
			'gender',
			'race',
			'ethnicity',
			'language_id',
			'status_marital',
			'status_employment',
			'employer',
			'employer_phone',
			'additional_phone',
			'point_of_contact_phone',
			'point_of_contact_phone_type',
			'home_country',
			'patient_id',
			'home_address',
			'home_apt_number',
			'home_state',
			'custom_home_state',
			'home_city',
			'custom_home_city',
			'home_zip_code',
			'home_phone',
			'home_email',
			'ec_name',
			'ec_relationship',
			'ec_phone_number',
			'home_phone_type',
			'additional_phone_type',
			'ec_phone_type',
			'parents_name',
			'secondary_diagnosis',
			'admission_type',
			'patients_relations',
			'admitting_diagnosis',
			'organization_id'
		];
	}

}
