<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class INS extends Segment
{
	const FIELD_SIZE = 17;
	const NAME = 'INS';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getYesNoConditionOrResponseCode()
	{
		return $this->collection[1];
	}

	public function getIndividualRelationshipCode()
	{
		return $this->collection[2];
	}

	public function getMaintenanceTypeCode()
	{
		return $this->collection[3];
	}

	public function getMaintenanceReasonCode()
	{
		return $this->collection[4];
	}

	public function getBenefitStatusCode()
	{
		return $this->collection[5];
	}

	public function getMedicareStatusCode()
	{
		return $this->collection[6];
	}

	public function getConsolidatedOmnibusBudgetReconciliationActQualifying()
	{
		return $this->collection[7];
	}

	public function getEmploymentStatusCode()
	{
		return $this->collection[8];
	}

	public function getStudentStatusCode()
	{
		return $this->collection[9];
	}

	public function getYesNoConditionOrResponseCode2()
	{
		return $this->collection[10];
	}

	public function getDateTimePeriodFormatQualifier()
	{
		return $this->collection[11];
	}

	public function getDateTimePeriod()
	{
		return $this->collection[12];
	}

	public function getConfidentialityCode()
	{
		return $this->collection[13];
	}

	public function getCityName()
	{
		return $this->collection[14];
	}

	public function getStateOrProvinceCode()
	{
		return $this->collection[15];
	}

	public function getCountryCode()
	{
		return $this->collection[16];
	}

	public function getNumber()
	{
		return $this->collection[17];
	}

	public function setYesNoConditionOrResponseCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setIndividualRelationshipCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setMaintenanceTypeCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setMaintenanceReasonCode($s)
	{
		$this->collection[4] = $s;
	}

	public function setBenefitStatusCode($s)
	{
		$this->collection[5] = $s;
	}

	public function setMedicareStatusCode($s)
	{
		$this->collection[6] = $s;
	}

	public function setConsolidatedOmnibusBudgetReconciliationActQualifying($s)
	{
		$this->collection[7] = $s;
	}

	public function setEmploymentStatusCode($s)
	{
		$this->collection[8] = $s;
	}

	public function setStudentStatusCode($s)
	{
		$this->collection[9] = $s;
	}

	public function setYesNoConditionOrResponseCode2($s)
	{
		$this->collection[10] = $s;
	}

	public function setDateTimePeriodFormatQualifier($s)
	{
		$this->collection[11] = $s;
	}

	public function setDateTimePeriod($s)
	{
		$this->collection[12] = $s;
	}

	public function setConfidentialityCode($s)
	{
		$this->collection[13] = $s;
	}

	public function setCityName($s)
	{
		$this->collection[14] = $s;
	}

	public function setStateOrProvinceCode($s)
	{
		$this->collection[15] = $s;
	}

	public function setCountryCode($s)
	{
		$this->collection[16] = $s;
	}

	public function setNumber($s)
	{
		$this->collection[17] = $s;
	}

	public function toArray()
	{
		return [
			'response_code' => $this->getYesNoConditionOrResponseCode(),
			'individualRelationshipCode' => $this->getIndividualRelationshipCode(),
			'maintenanceTypeCode' => $this->getMaintenanceTypeCode(),
			'maintenanceReasonCode' => $this->getMaintenanceReasonCode(),
			'benefitStatusCode' => $this->getBenefitStatusCode(),
			'medicareStatusCode' => $this->getMedicareStatusCode(),
			'consolidatedOmnibusBudgetReconciliationActQualifying' => $this->getConsolidatedOmnibusBudgetReconciliationActQualifying(),
			'employmentStatusCode' => $this->getEmploymentStatusCode(),
			'studentStatusCode' => $this->getStudentStatusCode(),
			'yesNoConditionOrResponseCode2' => $this->getYesNoConditionOrResponseCode2(),
			'dateTimePeriodFormatQualifier' => $this->getDateTimePeriodFormatQualifier(),
			'dateTimePeriod' => $this->getDateTimePeriod(),
			'confidentialityCode' => $this->getConfidentialityCode(),
			'cityName' => $this->getCityName(),
			'stateOrProvinceCode' => $this->getStateOrProvinceCode(),
			'countryCode' => $this->getCountryCode(),
			'number' => $this->getNumber(),

		];
	}
}