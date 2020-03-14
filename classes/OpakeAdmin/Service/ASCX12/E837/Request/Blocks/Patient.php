<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use Opake\Helper\TimeFormat;
use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class Patient extends AbstractRequestSegment
{

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	protected $codingInsurance;

	/**
	 * Patient constructor.
	 * @param \Opake\Model\Cases\Item $case
	 * @param \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $codingInsurance
	 */
	public function __construct(\Opake\Model\Cases\Item $case, \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $codingInsurance)
	{
		$this->case = $case;
		$this->codingInsurance = $codingInsurance;
	}

	protected function generateSegmentsBeforeChildren($data)
	{
		$insurance = $this->codingInsurance;
		$case = $this->case;
		$isPatientSubscriber = true;
		$relationshipToInsured = null;
		if ($insurance->getCaseInsurance()->isRegularInsurance()) {
			$insuranceData = $insurance->getCaseInsuranceDataModel();
			$isPatientSubscriber = ($insuranceData->relationship_to_insured == 0);
			$relationshipToInsured = $this->formatPatientType($insuranceData->relationship_to_insured);
		}


		if (!$isPatientSubscriber) {


			if (!$case->registration->first_name) {
				throw new \Exception('Patient\'s first name is not entered');
			}

			if (!$case->registration->last_name) {
				throw new \Exception('Patient\'s last name is not entered');
			}

			if (!$case->registration->home_address) {
				throw new \Exception('Patient\'s address is not entered');
			}

			if (!$case->registration->home_city->loaded()) {
				throw new \Exception('Patient\'s city is not entered');
			}

			if (!$case->registration->home_state->loaded()) {
				throw new \Exception('Patient\'s state is not selected');
			}

			if (!$case->registration->home_zip_code) {
				throw new \Exception('Patient\'s ZIP code is not entered');
			}

			if (!$case->registration->home_zip_code) {
				throw new \Exception('Patient\'s ZIP code is not entered');
			}

			if (!$case->registration->dob) {
				throw new \Exception('Patient\'s date of birth is not entered');
			}

			if (!$case->registration->gender) {
				throw new \Exception('Patient\'s gender is not entered');
			}

			$firstName = $case->registration->first_name;
			$lastName = $case->registration->last_name;
			$middleName = $case->registration->middle_name;
			$suffix = $case->registration->suffix;
			$address = $case->registration->home_address;
			$aptNumber = $case->registration->home_apt_number;
			$cityName = $case->registration->home_city->name;
			$stateCode = $case->registration->home_state->code;
			$zipCode = $case->registration->home_zip_code;
			$birthDate = $case->registration->dob;
			$gender = $case->registration->gender;

			$firstName = $this->prepareString($firstName, 35);
			$lastName = $this->prepareString($lastName, 60);
			$middleName = ($middleName) ? $this->prepareString($middleName, 25) : '';

			$suffixesList = \Opake\Model\Patient::getSuffixesList();
			$suffix = (isset($suffixesList[$suffix])) ? $this->prepareString($suffixesList[$suffix]) : '';

			$cityName = $this->prepareString($cityName, 30);
			$stateCode = $this->prepareString($stateCode, 2);

			$birthDate = TimeFormat::fromDBDate($birthDate);
			$birthDate = $birthDate->format('Ymd');

			$address = ($aptNumber) ? ($aptNumber . ' ' . $address) : $address;
			$address = $this->prepareAddress($address, 55);

			$zipCode = $this->prepareString($zipCode, 15);

			//Loop 2000B - Patient HL Loop
			$data[] = [
				'HL',
				'3',
				'2',
				'23',
				'0',
			];

			$data[] = [
				'PAT',
				$relationshipToInsured
			];

			//Loop 2010CA - Patient

			$data[] = [
				'NM1',
				'QC',
				'1',
				$lastName,
				$firstName,
				$middleName,
				'',
				$suffix,
			];

			$data[] = [
				'N3',
				$address[0],
			];

			$data[] = [
				'N4',
				$cityName,
				$stateCode,
				$zipCode
			];

			$data[] = [
				'DMG',
				'D8',
				$birthDate,
				$this->formatGender($gender)
			];
		}

		return $data;
	}

	protected function formatPatientType($patientType)
	{
		if ($patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_HUSBAND ||
			$patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_WIFE ||
			$patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_SPOUSE) {
			return '01';
		}

		if ($patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_CHILD) {
			return '19';
		}

		if ($patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_EMPLOYEE) {
			return '20';
		}

		if ($patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_UNKNOWN) {
			return '21';
		}

		if ($patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_ORGAN_DONOR) {
			return '39';
		}

		if ($patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_CADAVER_DONOR) {
			return '40';
		}

		if ($patientType == \Opake\Model\Cases\Registration::RELATIONSHIP_TO_INSURED_LIFE_PARTNER) {
			return '53';
		}

		return 'G8';
	}

	protected function formatGender($gender)
	{
		if ($gender == 1) {
			return 'M';
		}

		if ($gender == 2) {
			return 'F';
		}

		return 'U';
	}


}