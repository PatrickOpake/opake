<?php

namespace OpakeAdmin\Service\Navicure\HealthCare;

use OpakeAdmin\Service\Navicure\HealthCare\Exception\ValidationException;

class RequestParams
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var string
	 */
	protected $memberId;

	/**
	 * @var string
	 */
	protected $memberFirstName;

	/**
	 * @var string
	 */
	protected $patientFirstName;

	/**
	 * @var string
	 */
	protected $memberLastName;

	/**
	 * @var string
	 */
	protected $patientLastName;

	/**
	 * @var string
	 */
	protected $memberDateOfBirth;

	/**
	 * @var string
	 */
	protected $patientDateOfBirth;

	/**
	 * @var int
	 */
	protected $insurancePayorId;

	/**
	 * @var int
	 */
	protected $organizationId;

	/**
	 * @var \Opake\Model\Insurance\Payor
	 */
	protected $insurancePayor;

	/**
	 * @var int
	 */
	protected $insuranceType;

	/**
	 * @var \Opake\Model\Organization
	 */
	protected $organization;

	/**
	 * @var int
	 */
	protected $relationship_to_insured;

	protected $insuredUserState;

	protected $patientUserState;

	/**
	 * @param \Opake\Application $pixie
	 * @throws \Exception
	 */
	public function __construct($pixie, $form)
	{
		$this->pixie = $pixie;
		$this->memberId = $form->getValueByName('policy_num');
		$this->memberFirstName = $form->getValueByName('insured_first_name');
		$this->patientFirstName = $form->getValueByName('patient_first_name');
		$this->memberLastName = $form->getValueByName('insured_last_name');
		$this->patientLastName = $form->getValueByName('patient_last_name');
		$this->memberDateOfBirth = $form->getValueByName('insured_dob');
		$this->patientDateOfBirth = $form->getValueByName('patient_dob');
		$this->organizationId = $form->getValueByName('organization_id');
		$this->insuranceType = $form->getValueByName('type');
		$this->insurancePayorId = $form->getValueByName('payor_id');
		$this->relationship_to_insured = $form->getValueByName('relationship_to_insured');
		$this->insuredUserState = $form->getValueByName('insured_user_state');
		$this->patientUserState = $form->getValueByName('patient_user_state');
	}

	/**
	 * @return string
	 */
	public function getMemberId()
	{
		return $this->memberId;
	}

	/**
	 * @return string
	 */
	public function getMemberFirstName()
	{
		return $this->memberFirstName;
	}

	/**
	 * @return string
	 */
	public function getPatientFirstName()
	{
		return $this->patientFirstName;
	}

	/**
	 * @return string
	 */
	public function getMemberLastName()
	{
		return $this->memberLastName;
	}

	/**
	 * @return string
	 */
	public function getPatientLastName()
	{
		return $this->patientLastName;
	}

	/**
	 * @return string
	 */
	public function getMemberDateOfBirth()
	{
		return $this->memberDateOfBirth;
	}

	/**
	 * @return string
	 */
	public function getPatientDateOfBirth()
	{
		return $this->patientDateOfBirth;
	}

	/**
	 * @return int
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param int $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	/**
	 * @return \Opake\Model\Organization
	 * @throws \Exception
	 */
	public function getOrganization()
	{
		if (!$this->organization) {
			$this->organization = $this->pixie->orm->get('Organization', $this->organizationId);
			if (!$this->organization->loaded()) {
				throw new \Exception('Unknown organization');
			}
		}

		return $this->organization;
	}

	/**
	 * @return string
	 */
	public function getOrganizationNpi()
	{
		return $this->getOrganization()->npi;
	}

	/**
	 * @return string
	 */
	public function getOrganizationName()
	{
		return $this->getOrganization()->name;
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function getOrganizationServiceCodes()
	{
		$codesString = $this->getOrganization()->eligible_service_codes;
		if ($codesString) {
			$codes = explode(',', $codesString);
			$codes = array_map('trim', $codes);
			return $codes;
		}

		return [];
	}

	/**
	 * @return \Opake\Model\Insurance\Payor
	 * @throws \Exception
	 */
	public function getInsurancePayor()
	{
		if (!$this->insurancePayor) {
			$this->insurancePayor = $this->pixie->orm->get('Insurance_Payor', $this->insurancePayorId);
			if (!$this->insurancePayor->loaded()) {
				throw new ValidationException('Unknown payor');
			}
		}

		return $this->insurancePayor;
	}

	/**
	 * @return mixed
	 * @throws \Exception
	 */
	public function isRemotePayor()
	{
		return $this->getInsurancePayor()->is_remote_payor;
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function getPayorRemoteId()
	{
		return $this->getInsurancePayor()->remote_payor_id;
	}

	/**
	 * @return bool
	 */
	public function isMedicarePayor()
	{
		return $this->insuranceType == 2;
	}

	/**
	 * @return int
	 */
	public function getInsuranceType()
	{
		return $this->insuranceType;
	}


	public function getNavicurePayorId()
	{
		return $this->getInsurancePayor()->navicure_eligibility_payor_id;
	}

	public function isSelfRelation()
	{
		return !$this->relationship_to_insured;
	}
}