<?php

namespace Opake\ActivityLogger\Formatter\Booking\Row;

use Opake\ActivityLogger\Formatter\ArrayRowFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\Helper\StringHelper;
use Opake\Model\Insurance\AbstractType;
use Opake\Model\Patient;

class BookingInsuranceRowFormatter extends ArrayRowFormatter
{
	protected function formatLabel($id)
	{
		return 'Insurance #' . $id;
	}

	protected function formatValue($key, $value)
	{
		switch ($key) {

			// regular insurance fields
			case 'insurance_id':
				return FormatterHelper::formatInsuranceCompanyName($this->pixie, $value);

			case 'gender':
				return FormatterHelper::formatKeyValueSource($value, Patient::getGendersList());

			case 'country_id':
				return FormatterHelper::formatCountryName($this->pixie, $value);

			case 'state_id':
				return FormatterHelper::formatStateName($this->pixie, $value);

			case 'city_id':
				return FormatterHelper::formatCityName($this->pixie, $value);

			case 'relationship_to_insured':
				return FormatterHelper::formatKeyValueSource($value, Patient::getRelationshipToInsuredList());

			case 'type':
				return FormatterHelper::formatKeyValueSource($value, AbstractType::getInsuranceTypesList());

			case 'order':
				return FormatterHelper::formatKeyValueSource($value, AbstractType::getInsuranceOrderList());

			case 'dob':
				if ($value) {
					$value = substr((string) $value, 0, 10);
				}
				return FormatterHelper::formatDate($value);

			case 'is_primary':
				return FormatterHelper::formatKeyValueSource($value, Patient::getInsurancePrimaryList());

			case 'suffix':
				return FormatterHelper::formatKeyValueSource($value, Patient::getSuffixesList());

			case 'oon_benefits':
			case 'pre_certification_required':
			case 'pre_certification_obtained':
			case 'self_funded':
				return FormatterHelper::formatYesNo($value);

			// auto & worker accident fields
			case 'accident_date':
				return FormatterHelper::formatDate($value);

			//description insurance fields
			case 'description':
				return StringHelper::truncate($value, 200);

			case 'is_accident':
				return FormatterHelper::formatYesNo($value);

			case 'is_self_funded':
				return FormatterHelper::formatYesNo($value);

			case 'employer_city_id':
				return FormatterHelper::formatCityName($this->pixie, $value);

			case 'employer_state_id':
				return FormatterHelper::formatStateName($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'type' => 'Type',
			'order' => 'Order',
			'insurance_id' => 'Insurance',
		    'last_name' => 'Insured Last Name',
		    'first_name' => 'Insured First Name',
		    'middle_name' => 'M.I.',
		    'suffix' => 'Suffix',
		    'dob' => 'Date of Birth',
		    'gender' => 'Gender',
		    'address_insurance' => 'Insurance Company Address',
		    'address' => 'Address',
		    'apt_number' => 'Apt #',
		    'state_id' => 'State',
		    'custom_state' => 'State',
		    'city_id' => 'City',
		    'custom_city' => 'City',
		    'zip_code' => 'Zip',
		    'relationship_to_insured' => 'Relationship to Patient',
		    'policy_number' => 'Policy #',
		    'group_number' => 'Group #',
		    'provider_phone' => 'Provider Phone',
		    'insurance_verified' => 'Insurance Verified',
		    'is_pre_authorization_completed' => 'Pre Authorization Completed',
		    'description' => 'Description',
			'insurance_name' => 'Insurance Name',
		    'adjuster_name' => 'Adjuster Name',
		    'claim' => 'Claim #',
		    'adjuster_phone' => 'Adjuster Phone',
		    'insurance_company_phone' => 'Insurance Company Phone #',
		    'authorization_number' => 'Authorization #',
		    'insurance_address' => 'Auto Insurance Address',
		    'zip' => 'Zip',
		    'accident_date' => 'Accident Date',
		    'attorney_name' => 'Attorney Name',
		    'attorney_phone' => 'Attorney Phone',
		    'phone' => 'Phone',
		    'country_id' => 'Country',
		    'authorization_or_referral_number' => 'Authorization Code or Referral #',
		    'is_accident' => 'Accident?',
		    'employee_id' => 'Employee ID#',
		    'employer_name' => 'Employer Name',
		    'employer_address' => 'Employer Address',
		    'employer_city_id' => 'Employer City',
		    'employer_state_id' => 'Employer State',
		    'employer_zip' => 'Employer ZIP',
		    'is_self_funded' => 'Self Funded',
		    'cms1500_payer_id' => 'Electronic 1500 Payer ID #',
		    'ub04_payer_id' => 'Electronic UB04 Payer ID #',
		    'eligibility_payer_id' => 'Eligibiltiy Payer ID #'
		];
	}
}