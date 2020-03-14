<?php

namespace Opake\ActivityLogger\Action\Patient;

use Opake\ActivityLogger\Action\ModelAction;

class PatientChangeAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'patient' => $model->id()
		];
	}

	/**
	 * @return array
	 */
	protected function getSearchParams()
	{
		return [
			'patient_id' => $this->getExtractor()->getModel()->id()
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'title',
			'last_name',
			'first_name',
			'ssn',
			'status_marital',
			'middle_name',
			'suffix',
			'gender',
			'dob',
			'ethnicity',
			'race',
			'home_address',
			'home_apt_number',
			'language_id',
			'status_employment',
			'employer',
			'employer_phone',
			'home_country_id',
			'home_state_id',
			'home_city_id',
			'home_zip_code',
			'home_email',
			'ec_name',
			'ec_phone_number',
			'home_phone',
			'additional_phone',
			'ec_relationship',
			'point_of_contact_phone',
			'point_of_contact_phone_type',
		];
	}

}