<?php

namespace OpakeAdmin\Helper\Billing\Insurance;

use Opake\Model\Insurance\AbstractType;

abstract class AbstractInsurance
{
	/**
	 * @var AbstractType
	 */
	protected $caseInsurance;

	/**
	 * @var bool
	 */
	protected $usePayerDataIfMissed = false;

	/**
	 * @param AbstractType $caseInsurance
	 */
	public function __construct($caseInsurance)
	{
		$this->caseInsurance = $caseInsurance;
	}

	/**
	 * @return boolean
	 */
	public function isUsePayerDataIfMissed()
	{
		return $this->usePayerDataIfMissed;
	}

	/**
	 * @param boolean $usePayerDataIfMissed
	 */
	public function setUsePayerDataIfMissed($usePayerDataIfMissed)
	{
		$this->usePayerDataIfMissed = $usePayerDataIfMissed;
	}

	/**
	 * @return AbstractType
	 */
	public function getCaseInsurance()
	{
		return $this->caseInsurance;
	}

	/**
	 * @return \Opake\Model\AbstractModel
	 */
	public function getCaseInsuranceDataModel()
	{
		return $this->caseInsurance->getInsuranceDataModel();
	}

	abstract public function getAddress();

	abstract public function getCity();

	abstract public function getState();

	abstract public function getZipCode();

	abstract public function getInsuranceCompanyName();

	abstract public function getPolicyNumber();

	abstract public function getGroupNumber();

	abstract public function getProviderPhone();

	abstract public function getAuthorizationCodeOrReferralNumber();

	abstract public function getCarrierCode();

	abstract public function getCoInsurance();

	abstract public function getCoPay();

	abstract public function getPriorPayments();

	abstract public function getPrevAdjudicatedClaimNumber();

	abstract public function getCMS1500PayerId();

	abstract public function getUB04PayerId();

	abstract public function getEligibilityPayerId();

	/**
	 * @param AbstractType $insurance
	 * @return \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance
	 */
	public static function wrapCaseInsurance($insurance)
	{
		if ($insurance->isRegularInsurance()) {
			return new \OpakeAdmin\Helper\Billing\Insurance\RegularInsurance($insurance);
		} else if ($insurance->isAutoAccidentInsurance()) {
			return new \OpakeAdmin\Helper\Billing\Insurance\AutoAccident($insurance);
		} else if ($insurance->isWorkersCompanyInsurance()) {
			return new \OpakeAdmin\Helper\Billing\Insurance\WorkersComp($insurance);
		}

		return new \OpakeAdmin\Helper\Billing\Insurance\Blank($insurance);
	}
}