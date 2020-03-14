<?php

namespace Opake\ActivityLogger\Formatter\Intake\Patient;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;
use Opake\Model\Cases\Registration;
use Opake\Model\Patient;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'dob':
				return FormatterHelper::formatDate($value);
			case 'language_id':
				return FormatterHelper::formatLanguage($this->pixie, $value);
			case 'title':
				return FormatterHelper::formatKeyValueSource($value, Patient::getTitlesList());
			case 'suffix':
				return FormatterHelper::formatKeyValueSource($value, Patient::getSuffixesList());
			case 'gender':
				return FormatterHelper::formatKeyValueSource($value, Patient::getGendersList());
			case'race':
				return FormatterHelper::formatKeyValueSource($value, Patient::getRacesList());
			case'ethnicity':
				return FormatterHelper::formatKeyValueSource($value, Patient::getEthnicityList());
			case 'status_marital':
				return FormatterHelper::formatKeyValueSource($value, Patient::getMartialStatusesList());
			case 'status_employment':
				return FormatterHelper::formatKeyValueSource($value, Patient::getEmploymentStatusesList());
			case 'home_country_id':
				return FormatterHelper::formatCountryName($this->pixie, $value);
			case 'home_state_id':
				return FormatterHelper::formatStateName($this->pixie, $value);
			case 'home_city_id':
				return FormatterHelper::formatCityName($this->pixie, $value);
			case 'ec_relationship':
				return FormatterHelper::formatKeyValueSource($value, Patient::getRelationshipToInsuredList());
			case 'diagnosis':
				return FormatterHelper::formatICD($this->pixie, $value);
			case 'admission_type':
				return FormatterHelper::formatKeyValueSource($value, Registration::getAdmissionTypesList());
			case 'patients_relations':
				return FormatterHelper::formatKeyValueSource($value, Registration::getPatientRelationsList());
			case 'admitting_diagnosis':
				return FormatterHelper::formatICDList($this->pixie, $value);
			case 'ec_phone_number':
			case 'employer_phone':
			case 'home_phone':
			case 'additional_phone':
			case 'point_of_contact_phone':
				return FormatterHelper::formatPhone($value);
			case 'point_of_contact_phone_type' :
				return FormatterHelper::formatKeyValueSource($value, Patient::getPhoneTypes());

		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'title' => 'Title',
			'last_name' => 'Last Name',
			'first_name' => 'First Name',
			'ssn' => 'Social Security #',
			'status_marital' => 'Marital Status',
			'middle_name' => 'M.I.',
			'suffix' => 'Suffix',
			'gender' => 'Gender',
			'dob' => 'Date of Birth',
			'ethnicity' => 'Ethnicity',
			'race' => 'Race',
			'home_address' => 'Address',
			'home_apt_number' => 'Apt #',
			'language_id' => 'Preferred Language',
			'status_employment' => 'Occupation',
			'employer' => 'Employer/School',
			'employer_phone' => 'Employer Phone #',
			'home_country_id' => 'Country',
			'home_state_id' => 'State',
			'home_city_id' => 'City',
			'home_zip_code' => 'ZIP code',
			'home_email' => 'Email',
			'ec_name' => 'Emergency Contact',
			'ec_phone_number' => 'Emergency Phone #',
			'home_phone' => 'Phone #',
			'additional_phone' => 'Additional Phone #',
			'ec_relationship' => 'Relationship to Patient',
			'admitting_diagnosis' => 'Admitting Diagnosis',
			'admission_type' => 'Admission Type',
			'patients_relations' => "Patient's Relations",
			'point_of_contact_phone' => "Point of Contact SMS #",
			'point_of_contact_phone_type' => "Point of Contact SMS Type",
		];
	}
}