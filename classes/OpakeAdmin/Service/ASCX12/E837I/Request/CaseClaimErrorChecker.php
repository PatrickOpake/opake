<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request;

use Opake\Model\Insurance\AbstractType;

class CaseClaimErrorChecker
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;
	protected $errors = [];

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct(\Opake\Model\Cases\Item $case)
	{
		$this->case = $case;
	}

	public function checkErrors()
	{
		$this->errors = [];

		$this->checkCase();
		$this->checkInsurance();
		$this->checkBillingProvider();
		$this->checkPrimaryUser();
		$this->checkOrganizationAndSite();
		$this->checkBills();

		return array_unique($this->errors);
	}

	protected function checkInsurance()
	{
		if (!$this->case->coding->isPrimaryInsuranceAssigned()) {
			$orderList = AbstractType::getInsuranceOrderList();
			$insuranceTypeLabel = '';
			if(isset($orderList[$this->case->coding->insurance_order])) {
				$insuranceTypeLabel = $orderList[$this->case->coding->insurance_order] . ' insurance';
			}
			$codingInsurance = $this->case->coding->getAssignedInsurance();
		} else {
			$insuranceTypeLabel = 'primary insurance';
			$codingInsurance = $this->case->coding->getPrimaryInsurance();
		}

		if (!$codingInsurance) {
			$this->addError('Case has no active ' . $insuranceTypeLabel . ' selected');
			return;
		}
		$primaryInsurance = $codingInsurance->getCaseInsurance();
		if (!$primaryInsurance || !$primaryInsurance->loaded()) {
			$this->addError('Case has no active ' . $insuranceTypeLabel . ' selected');
			return;
		}
		if ($primaryInsurance->isDescriptionInsurance()) {
			$this->errors[] = 'Unsupported ' . $insuranceTypeLabel . ' type';
			return;
		}

		if (!$codingInsurance->getInsuranceCompanyName()) {
			$this->errors[] = 'Insurance company name is not entered';
		}

		$this->checkNavicureCode($codingInsurance);
		$this->checkSubscriberAndPatient($primaryInsurance);
	}

	protected function checkNavicureCode($codingInsurance)
	{
		if (!$codingInsurance->getUB04PayerId()) {
			$this->addError('Electronic UB04 Payer ID is not entered');
		}
	}

	protected function checkBillingProvider()
	{
		$site = $this->case->location->site;

		if (!$site || !$site->loaded()) {
			$this->errors[] = 'Site for case is not defined';
			return;
		}

		if (!$site->name) {
			$this->errors[] = 'Site Name is not filled for site ' . $site->name;
		}

		if (!$site->npi) {
			$this->errors[] = 'NPI is not filled for site ' . $site->name;
		}

		if (!$site->federal_tax) {
			$this->errors[] = 'TIN is not filled for site ' . $site->name;
		}

		if (!$site->state->loaded()) {
			$this->errors[] = 'State is not entered for site ' . $site->name;
		}

		if (!$site->city->loaded()) {
			$this->errors[] = 'City is not entered for site ' . $site->name;
		}

		if (!$site->address) {
			$this->errors[] = 'Address is not entered for site ' . $site->name;
		}

		if (!$site->zip_code) {
			$this->errors[] = 'Zip-code is not entered for site ' . $site->name;
		}
	}

	protected function checkPrimaryUser()
	{
		$primaryUser = $this->case->getFirstSurgeon();

		if (!$primaryUser || !$primaryUser->loaded()) {
			$this->errors[] = 'Primary user for case is not defined';
			return;
		}

		if (!$primaryUser->credentials->npi_number) {
			$this->errors[] = 'NPI is not filled for user ' . $primaryUser->getFullName();
		}
	}

	protected function checkSubscriberAndPatient($insurance)
	{
		$case = $this->case;
		$isPatientSubscriber = true;
		if ($insurance->isRegularInsurance()) {
			$insuranceData = $insurance->getInsuranceDataModel();
			$relationshipToInsured = $insuranceData->relationship_to_insured;
			$isPatientSubscriber = ($relationshipToInsured == 0);
		}

		if (!$case->registration->first_name) {
			$this->addError('Patient\'s first name is not entered');
		}

		if (!$case->registration->last_name) {
			$this->addError('Patient\'s last name is not entered');
		}

		if (!$case->registration->home_address) {
			$this->addError('Patient\'s address is not entered');
		}

		if (!$case->registration->home_city->loaded()) {
			$this->addError('Patient\'s city is not entered');
		}

		if (!$case->registration->home_state->loaded()) {
			$this->addError('Patient\'s state is not selected');
		}

		if (!$case->registration->home_zip_code) {
			$this->addError('Patient\'s ZIP code is not entered');
		}

		if (!$case->registration->dob) {
			$this->addError('Patient\'s date of birth is not entered');
		}

		if (!$case->registration->gender) {
			$this->addError('Patient\'s gender is not entered');
		}

		//self
		if (!$isPatientSubscriber) {

			$insuranceData = $insurance->getInsuranceDataModel();

			if (!$insuranceData->first_name) {
				$this->addError('Subscriber\'s first name is not entered');
			}

			if (!$insuranceData->last_name) {
				$this->addError('Subscriber\'s last name is not entered');
			}

			if (!$insuranceData->address) {
				$this->addError('Subscriber\'s address is not entered');
			}

			if (!$insuranceData->city->loaded()) {
				$this->addError('Subscriber\'s city is not selected');
			}

			if (!$insuranceData->state->loaded()) {
				$this->addError('Subscriber\'s state is not selected');
			}

			if (!$insuranceData->zip_code) {
				$this->addError('Subscriber\'s ZIP code is not entered');
			}

			if (!$insuranceData->dob) {
				$this->addError('Subscriber\'s date of birth is not entered');
			}

			if (!$insuranceData->gender) {
				$this->addError('Subscriber\'s gender is not entered');
			}

		}
	}

	protected function checkOrganizationAndSite()
	{

		$caseSite = $this->case->location->site;

		if (!$caseSite->contact_name) {
			$this->addError('Contact name is not entered for the site');
		}

		if (!$caseSite->contact_phone) {
			$this->addError('Contact phone is not entered for the site');
		}

		if (!$caseSite->navicure_sftp_username || !$caseSite->navicure_sftp_password) {
			$this->addError('Navicure SFTP credentials for ' . $caseSite->name . ' are not set');
		}
	}

	protected function checkCase()
	{
		if (!$this->case->coding->discharge_code->loaded()) {
			$this->addError('Discharge is required for the Electronic UB04 claim');
		}
	}

	protected function checkBills()
	{
		$bills = $this->case->coding->getBills();
		foreach ($bills as $bill) {
			if (!$bill->revenue_code) {
				$this->addError('Revenue code for each procedure is required for the Electronic UB04 claim');
			}
		}
	}

	protected function addError($message)
	{
		$this->errors[] = $message;
	}

}