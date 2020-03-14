<?php

namespace OpakeAdmin\Form\Booking;

use Opake\Form\AbstractForm;

class PatientForm extends AbstractForm
{

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');
		$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');
		$validator->field('dob')->rule('date')->error('Incorrect Date of Birth format');
	}

	protected function getFields()
	{
		return [
			'first_name',
			'middle_name',
			'last_name',
			'suffix',
			'parents_name',
			'home_address',
			'home_apt_number',
			'home_state',
			'custom_home_state',
			'home_city',
			'custom_home_city',
			'home_zip_code',
			'home_country',
			'home_phone',
			'home_phone_type',
			'additional_phone',
			'additional_phone_type',
			'home_email',
			'point_of_contact_phone',
			'point_of_contact_phone_type',
			'dob',
			'ssn',
			'gender',
			'status_marital',
			'ec_name',
			'relationship',
			'ec_relationship',
			'ec_phone_number',
			'ec_phone_type',
		    'organization_id'
		];
	}


}
