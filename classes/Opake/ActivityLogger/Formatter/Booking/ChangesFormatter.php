<?php

namespace Opake\ActivityLogger\Formatter\Booking;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\Model\Booking;
use Opake\Model\Cases\Item;
use Opake\Model\Cases\Registration;
use Opake\Model\Patient;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'dob':
			case 'unable_to_work_from':
			case 'unable_to_work_to':
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
			case 'other_staff':
			case 'users':
			case 'assistant':
				return FormatterHelper::formatUsersList($this->pixie, $value);
			case 'home_phone_type':
			case 'ec_phone_type':
			case 'additional_phone_type':
			case 'point_of_contact_phone_type':
				return FormatterHelper::formatKeyValueSource($value, Patient::getPhoneTypes());
			case 'status_martial':
				return FormatterHelper::formatKeyValueSource($value, Patient::getMartialStatusesList());
			case 'time_start':
			case 'time_end':
				return FormatterHelper::formatDateAndTime($value);
			case 'room_id':
				return FormatterHelper::formatLocation($this->pixie, $value);
			case 'admission_type':
				return FormatterHelper::formatKeyValueSource($value, Registration::getAdmissionTypesList());
			case 'location':
				return FormatterHelper::formatKeyValueSource($value, Booking::getLocationList());
			case 'anesthesia_type':
				return FormatterHelper::formatKeyValueSource($value, Item::getAnesthesiaTypeList());
			case 'additional_cpts':
				return FormatterHelper::formatProceduresList($this->pixie, $value);
			case 'admitting_diagnosis':
			case 'secondary_diagnosis':
				return FormatterHelper::formatICDList($this->pixie, $value);
			case 'pre_op_required_data':
				return FormatterHelper::formatKeyValueSourceMultiple($value, Booking::getPreOpRequiredList());
			case 'transportation':
			case 'implants_flag':
			case 'is_unable_to_work':
			case 'special_equipment_flag':
				return FormatterHelper::formatYesNo($value);
			case 'insurances':
				return FormatterHelper::formatArrayOfChanges($this->pixie, $value , '\Opake\ActivityLogger\Formatter\Booking\Row\BookingInsuranceRowFormatter');
			case 'studies_ordered':
				return FormatterHelper::formatKeyValueSourceMultiple($value, Booking::getStudiesOrderedList());
			case 'relationship':
				return FormatterHelper::formatKeyValueSource($value, Patient::getRelationshipList());
			case 'implant_items':
			case 'equipments':
				return FormatterHelper::formatInventoryItemNameList($this->pixie, $value);
			case 'home_phone':
			case 'additional_phone':
			case 'ec_phone_number':
			case 'point_of_contact_phone':
				return FormatterHelper::formatPhone($value);
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'patient' => 'Patient',
			'last_name' => 'Last Name',
			'first_name' => 'First Name',
			'middle_name' => 'M.I.',
			'suffix' => 'Suffix',
			'parents_name' => 'Parent\'s Name',
			'home_address' => 'Address',
			'home_apt_number' => 'Apt #',
			'custom_home_state' => 'State',
			'custom_home_city' => 'City',
			'home_state_id' => 'State',
			'home_city_id' => 'City',
			'home_zip_code' => 'Zip Code',
			'home_country_id' => 'Country',
			'home_phone' => 'Phone #',
			'home_phone_type' => 'Phone Type',
			'additional_phone' => 'Additional Phone #',
			'additional_phone_type' => 'Additional Phone Type',
			'home_email' => 'Email',
			'dob' => 'Date of Birth',
			'ssn' => 'Social Security #',
			'gender' => 'Gender',
			'status_marital' => 'Marital Status',
			'relationship' => 'Relationship',
			'ec_name' => 'Emergency Contact',
			'ec_phone_number' => 'Emergency Phone #',
			'ec_phone_type' => 'Emergency Phone Type',
			'users' => 'Surgeon',
			'assistant' => 'Surgeon Assistant',
			'other_staff' => 'Other Staff',
			'admission_type' => 'Admission Type',
			'room_id' => 'Room',
			'time_start' => 'Time Start',
			'time_end' => 'Time End',
			'additional_cpts' => 'Proposed Procedure Codes',
			'location' => 'Location',
			'admitting_diagnosis' => 'Primary Diagnosis',
			'secondary_diagnosis' => 'Secondary Diagnosis',
			'pre_op_required_data' => 'Pre-Op Required Data',
			'studies_ordered' => 'Studies Ordered',
			'studies_other' => 'Studies (Other)',
			'anesthesia_type' => 'Anesthesia Type',
			'anesthesia_other' => 'Anesthesia (other)',
			'special_equipment_flag' => 'Special Equipment',
			'special_equipment_implants' => 'Special Equipment Description',
			'implants_flag' => 'Implants',
			'implants' => 'Implants Description',
			'transportation' => 'Transportation',
			'transportation_notes' => 'Transport',
			'description' => 'Description',
		    'insurances' => 'Insurances',
			'implant_items' => 'Implant Options',
			'equipments' => 'Equipment Options',
			'point_of_contact_phone' => 'Point of Contact SMS #',
			'point_of_contact_phone_type' => 'Point of Contact SMS Type',
			'is_unable_to_work' => 'Is patient employed and unable to work due to this illness?',
			'unable_to_work_from' => 'Unable to work From',
			'unable_to_work_to' => 'Unable to work To',
		];
	}
}