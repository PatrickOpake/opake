<?php

namespace OpakePatients\Model\Cases;

use Opake\Helper\TimeFormat;

class Registration extends \Opake\Model\Cases\Registration
{

	const PATIENT_APPOINTMENT_STATUS_BEGIN = 0;
	const PATIENT_APPOINTMENT_STATUS_CONTINUE = 1;
	const PATIENT_APPOINTMENT_STATUS_COMPLETE = 2;

	public function __construct($pixie)
	{
		$this->has_one['patient_confirm'] = [
			'model' => 'Patient_Appointment_Confirm',
			'key' => 'case_registration_id'
		];

		$this->has_one['alert_hidden'] = [
			'model' => 'Patient_Appointment_AlertHidden',
			'key' => 'case_registration_id'
		];

		parent::__construct($pixie);
	}

	public function toArray()
	{

		$insurances = [];
		foreach ($this->insurances->find_all() as $insurance) {
			$insurances[] = $insurance->toArray();
		}

		$data = [
			'id' => $this->id(),
			'status' => $this->status,
			'case' => $this->case->toArray(),
			'insurances' => $insurances,
			'title' => $this->title,
			'last_name' => $this->last_name,
			'first_name' => $this->first_name,
			'middle_name' => $this->middle_name,
			'suffix' => $this->suffix,
			'gender' => $this->gender,
			'dob' => $this->dob,
			'home_address' => $this->home_address,
			'home_apt_number' => $this->home_apt_number,
			'home_country' => $this->home_country->toArray(),
			'home_state' => $this->home_state->toArray(),
			'home_city' => $this->home_city->toArray(),
			'custom_home_state' => $this->custom_home_state,
			'custom_home_city' => $this->custom_home_city,
			'home_country_id' => $this->home_country_id,
			'home_state_id' => $this->home_state_id,
			'home_city_id' => $this->home_city_id,
			'home_zip_code' => $this->home_zip_code,
			'home_email' => $this->home_email,
			'home_phone' => $this->home_phone,
			'additional_phone' => $this->additional_phone,
			'ssn' => $this->ssn,
			'status_marital' => $this->status_marital,
			'ethnicity' => $this->ethnicity,
			'race' => $this->race,
			'language' => $this->language->toArray(),
			'status_employment' => $this->status_employment,
			'employer' => $this->employer,
			'employer_phone' => $this->employer_phone,
			'ec_name' => $this->ec_name,
			'ec_phone_number' => $this->ec_phone_number,
			'ec_relationship' => $this->ec_relationship,
			'is_patient_info_confirmed' => $this->isPatientInfoConfirmed(),
			'is_insurances_confirmed' => $this->isInsurancesConfirmed(),
			'is_forms_confirmed' => $this->isFormsConfirmed(),
		    'patient_appointment_status' => $this->getPatientAppointmentStatus()
		];

		return $data;
	}

	public function getPatientAppointmentStatus()
	{
		$isPatientInfoConfirmed = $this->isPatientInfoConfirmed();
		$isInsuranceConfirmed = $this->isInsurancesConfirmed();
		$isFormsConfirmed = $this->isFormsConfirmed();

		if ($isPatientInfoConfirmed && $isInsuranceConfirmed && $isFormsConfirmed) {
			return Registration::PATIENT_APPOINTMENT_STATUS_COMPLETE;
		}

		if ($isPatientInfoConfirmed || $isInsuranceConfirmed || $isFormsConfirmed) {
			return Registration::PATIENT_APPOINTMENT_STATUS_CONTINUE;
		}

		return Registration::PATIENT_APPOINTMENT_STATUS_BEGIN;
	}

	/**
	 * @return bool
	 */
	public function isPatientInfoConfirmed()
	{
		if ($this->patient_confirm && $this->patient_confirm->loaded()) {
			return (bool) $this->patient_confirm->is_patient_info_confirmed;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isInsurancesConfirmed()
	{
		if ($this->patient_confirm && $this->patient_confirm->loaded()) {
			return (bool) $this->patient_confirm->is_insurances_confirmed;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isFormsConfirmed()
	{
		if ($this->patient_confirm && $this->patient_confirm->loaded()) {
			return (bool) $this->patient_confirm->is_forms_confirmed;
		}

		return false;
	}


}