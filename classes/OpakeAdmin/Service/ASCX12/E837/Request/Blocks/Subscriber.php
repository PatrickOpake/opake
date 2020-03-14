<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use Opake\Helper\TimeFormat;
use Opake\Model\Insurance\AbstractType;
use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class Subscriber extends AbstractRequestSegment
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
	 * Subscriber constructor.
	 *
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
		$case = $this->case;
		$insurance = $this->codingInsurance;

		//Loop 2000B - Subscriber HL Loop

		$isPatientSubscriber = true;
		if ($insurance->getCaseInsurance()->isRegularInsurance()) {
			$insuranceData = $insurance->getCaseInsuranceDataModel();
			$relationshipToInsured = $insuranceData->relationship_to_insured;
			$isPatientSubscriber = ($relationshipToInsured == 0);
		}

		if ($insurance->getCaseInsurance()->isAutoAccidentInsurance()) {
			$claimFillingIndicatorType = 'AM';
		} else if ($insurance->getCaseInsurance()->isWorkersCompanyInsurance()) {
			$claimFillingIndicatorType = 'WC';
		} else if ($insurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_MEDICARE) {
			$claimFillingIndicatorType = 'MB';
		} else if ($insurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_MEDICAID) {
			$claimFillingIndicatorType = 'MC';
		} else {
			$claimFillingIndicatorType = 'CI';
		}

		//self
		if ($isPatientSubscriber) {

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

		} else {

			$insuranceData = $insurance->getCaseInsuranceDataModel();

			if (!$insuranceData->first_name) {
				throw new \Exception('Subscriber\'s first name is not entered');
			}

			if (!$insuranceData->last_name) {
				throw new \Exception('Subscriber\'s last name is not entered');
			}

			if (!$insuranceData->address) {
				throw new \Exception('Subscriber\'s address is not entered');
			}

			if (!$insuranceData->city->loaded()) {
				throw new \Exception('Subscriber\'s city is not selected');
			}

			if (!$insuranceData->state->loaded()) {
				throw new \Exception('Subscriber\'s state is not selected');
			}

			if (!$insuranceData->zip_code) {
				throw new \Exception('Subscriber\'s ZIP code is not entered');
			}

			if (!$insuranceData->dob) {
				throw new \Exception('Subscriber\'s date of birth is not entered');
			}

			if (!$insuranceData->gender) {
				throw new \Exception('Subscriber\'s gender is not entered');
			}

			$firstName = $insuranceData->first_name;
			$lastName = $insuranceData->last_name;
			$middleName = $insuranceData->middle_name;
			$suffix = $insuranceData->suffix;
			$address = $insuranceData->address;
			$aptNumber = $insuranceData->apt_number;
			$cityName = $insuranceData->city->name;
			$stateCode = $insuranceData->state->code;
			$zipCode = $insuranceData->zip_code;
			$birthDate = $insuranceData->dob;
			$gender = $insuranceData->gender;
		}

		$firstName = $this->prepareString($firstName, 35);
		$lastName = $this->prepareString($lastName, 60);
		$middleName = ($middleName) ? $this->prepareString($middleName, 25) : '';

		$suffixesList = \Opake\Model\Patient::getSuffixesList();
		$suffix = (isset($suffixesList[$suffix])) ? $this->prepareString($suffixesList[$suffix]) : '';

		$cityName = $this->prepareString($cityName, 30);
		$stateCode = $this->prepareString($stateCode, 2);

		$birthDate = TimeFormat::fromDBDate($birthDate);
		$birthDate = $birthDate->format('Ymd');
		$zipCode = $this->prepareString($zipCode, 15);

		$data[] = [
			'HL',
			'2', // Second HL
			'1',
			'22', //Subscriber
			'1' //Additional data fields
		];

		$data[] = [
			'SBR',
			'P',
			($isPatientSubscriber) ? '18' : '',
			$insurance->getGroupNumber() ? $this->prepareAlphaNumberic($insurance->getGroupNumber(), 50) : '',
			'',
			'',
			'',
			'',
			'',
			$claimFillingIndicatorType
		];

		$data[] = [
			'NM1',
			'IL',
			'1',
			$lastName,
			$firstName,
			$middleName,
			'',
			$suffix,
			'MI',
			$this->prepareAlphaNumberic($insurance->getPolicyNumber(), 80)
		];

		$address = ($aptNumber) ? ($aptNumber . ' ' . $address) : $address;
		$address = $this->prepareAddress($address, 55);

		$data[] = [
			'N3',
			$address[0]
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

		return $data;
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