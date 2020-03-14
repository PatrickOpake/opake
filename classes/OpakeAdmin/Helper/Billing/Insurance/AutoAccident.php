<?php

namespace OpakeAdmin\Helper\Billing\Insurance;

class AutoAccident extends AbstractInsurance
{
	public function getAddress()
	{
		$address =  $this->getCaseInsuranceDataModel()->insurance_address;
		if (!$address && $this->usePayerDataIfMissed) {
			$address = $this->getCaseInsuranceDataModel()->insurance_company->address;
		}
		return $address;
	}

	public function getCity()
	{
		$city =  $this->getCaseInsuranceDataModel()->city->loaded() ?
			$this->getCaseInsuranceDataModel()->city : null;

		if (!$city && $this->usePayerDataIfMissed) {
			$city = $this->getCaseInsuranceDataModel()->insurance_company->city->loaded() ?
				$this->getCaseInsuranceDataModel()->insurance_company->city : null;
		}

		return $city;
	}

	public function getState()
	{
		$state = $this->getCaseInsuranceDataModel()->state->loaded() ?
			$this->getCaseInsuranceDataModel()->state : null;

		if (!$state && $this->usePayerDataIfMissed) {
			$state = $this->getCaseInsuranceDataModel()->insurance_company->state->loaded() ?
				$this->getCaseInsuranceDataModel()->insurance_company->state : null;
		}

		return $state;
	}

	public function getZipCode()
	{
		$zipCode = $this->getCaseInsuranceDataModel()->zip;

		if (!$zipCode && $this->usePayerDataIfMissed) {
			$zipCode = $this->getCaseInsuranceDataModel()->insurance_company->zip_code;
		}

		return $zipCode;

	}

	public function getInsuranceCompanyName()
	{
		$insuranceCompanyName = $this->getCaseInsuranceDataModel()->insurance_company->name;
		if ($insuranceCompanyName) {
			return $insuranceCompanyName;
		}

		$insuranceCompanyName = $this->getCaseInsuranceDataModel()->insurance_name;
		return $insuranceCompanyName;
	}

	public function getPolicyNumber()
	{
		return $this->getCaseInsuranceDataModel()->claim;
	}

	public function getGroupNumber()
	{

	}

	public function getProviderPhone()
	{

	}

	public function getAuthorizationCodeOrReferralNumber()
	{

	}

	public function getCarrierCode()
	{

	}

	public function getCoInsurance()
	{

	}

	public function getCoPay()
	{

	}

	public function getPriorPayments()
	{

	}

	public function getPrevAdjudicatedClaimNumber()
	{

	}

	public function getCMS1500PayerId()
	{
		$code = $this->getCaseInsuranceDataModel()->cms1500_payer_id;
		if (!$code && $this->usePayerDataIfMissed) {
			$code = $this->getCaseInsuranceDataModel()->insurance_company->cms1500_payer_id;
			if ($code) {
				$code = $this->getCaseInsuranceDataModel()->insurance_company->navicure_payor_id;
			}
		}

		return $code;
	}

	public function getUB04PayerId()
	{
		$code = $this->getCaseInsuranceDataModel()->ub04_payer_id;
		if (!$code && $this->usePayerDataIfMissed) {
			$code = $this->getCaseInsuranceDataModel()->insurance_company->ub04_payer_id;
		}

		return $code;
	}

	public function getEligibilityPayerId()
	{
		$code = $this->getCaseInsuranceDataModel()->eligibility_payer_id;
		if (!$code && $this->usePayerDataIfMissed) {
			$code = $this->getCaseInsuranceDataModel()->insurance_company->navicure_eligibility_payor_id;
		}

		return $code;
	}
}