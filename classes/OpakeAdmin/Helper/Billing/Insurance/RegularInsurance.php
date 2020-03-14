<?php

namespace OpakeAdmin\Helper\Billing\Insurance;

class RegularInsurance extends AbstractInsurance
{
	public function getAddress()
	{
		$address = $this->getCaseInsuranceDataModel()->address_insurance;

		if (!$address && $this->usePayerDataIfMissed) {
			$address = $this->getCaseInsuranceDataModel()->insurance->address;
		}

		return $address;
	}

	public function getCity()
	{
		$city = $this->getCaseInsuranceDataModel()->insurance_city->loaded() ?
			$this->getCaseInsuranceDataModel()->insurance_city : null;

		if (!$city && $this->usePayerDataIfMissed) {
			$city = $this->getCaseInsuranceDataModel()->insurance->city->loaded() ?
				$this->getCaseInsuranceDataModel()->insurance->city : null;
		}

		return $city;

	}

	public function getState()
	{
		$state =  $this->getCaseInsuranceDataModel()->insurance_state->loaded() ?
			$this->getCaseInsuranceDataModel()->insurance_state : null;

		if (!$state && $this->usePayerDataIfMissed) {
			$state = $this->getCaseInsuranceDataModel()->insurance->state->loaded() ?
				$this->getCaseInsuranceDataModel()->insurance->state : null;
		}

		return $state;
	}

	public function getZipCode()
	{
		$zipCode = $this->getCaseInsuranceDataModel()->insurance_zip_code;
		if (!$zipCode && $this->usePayerDataIfMissed) {
			$zipCode = $this->getCaseInsuranceDataModel()->insurance->zip_code;
		}

		return $zipCode;
	}

	public function getInsuranceCompanyName()
	{
		$insuranceCompanyName = $this->getCaseInsuranceDataModel()->insurance->name;
		if ($insuranceCompanyName) {
			return $insuranceCompanyName;
		}

		$insuranceCompanyName = $this->getCaseInsuranceDataModel()->insurance_company_name;
		return $insuranceCompanyName;
	}

	public function getPolicyNumber()
	{
		return $this->getCaseInsuranceDataModel()->policy_number;
	}

	public function getGroupNumber()
	{
		return $this->getCaseInsuranceDataModel()->group_number;
	}

	public function getProviderPhone()
	{
		return $this->getCaseInsuranceDataModel()->provider_phone;
	}

	public function getAuthorizationCodeOrReferralNumber()
	{
		return $this->getCaseInsuranceDataModel()->authorization_or_referral_number;
	}

	public function getCarrierCode()
	{
		return $this->getCaseInsuranceDataModel()->insurance->carrier_code;
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
			$code = $this->getCaseInsuranceDataModel()->insurance->cms1500_payer_id;
			if ($code) {
				$code = $this->getCaseInsuranceDataModel()->insurance->navicure_payor_id;
			}
		}

		return $code;
	}

	public function getUB04PayerId()
	{
		$code = $this->getCaseInsuranceDataModel()->ub04_payer_id;
		if (!$code && $this->usePayerDataIfMissed) {
			$code = $this->getCaseInsuranceDataModel()->insurance->ub04_payer_id;
		}

		return $code;
	}

	public function getEligibilityPayerId()
	{
		$code = $this->getCaseInsuranceDataModel()->eligibility_payer_id;
		if (!$code && $this->usePayerDataIfMissed) {
			$code = $this->getCaseInsuranceDataModel()->insurance->navicure_eligibility_payor_id;
		}

		return $code;
	}
}