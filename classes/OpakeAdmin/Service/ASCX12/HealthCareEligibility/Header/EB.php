<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class EB extends Segment
{
	const FIELD_SIZE = 14;
	const NAME = 'EB';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getEligibilityOrBenefitInformationCode()
	{
		return $this->collection[1];
	}

	public function getCoverageLevelCode()
	{
		return $this->collection[2];
	}

	public function getServiceTypeCode()
	{
		return $this->collection[3];
	}

	public function getInsuranceTypeCode()
	{
		return $this->collection[4];
	}

	public function getPlanCoverageDescription()
	{
		return $this->collection[5];
	}

	public function getTimePeriodQualifier()
	{
		return $this->collection[6];
	}

	public function getMonetaryAmount()
	{
		return $this->collection[7];
	}

	public function getPercentageAsDecimal()
	{
		return $this->collection[8];
	}

	public function getQuantityQualifier()
	{
		return $this->collection[9];
	}

	public function getQuantity()
	{
		return $this->collection[10];
	}

	public function getYesNoConditionOrResponseCode()
	{
		return $this->collection[11];
	}

	public function getYesNoConditionOrResponseCode2()
	{
		return $this->collection[12];
	}

	public function getCompositeMedicalProcedureIdentifier()
	{
		return $this->collection[13];
	}

	public function getCompositeDiagnosisCodePointer()
	{
		return $this->collection[14];
	}

	public function setEligibilityOrBenefitInformationCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setCoverageLevelCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setServiceTypeCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setInsuranceTypeCode($s)
	{
		$this->collection[4] = $s;
	}

	public function setPlanCoverageDescription($s)
	{
		$this->collection[5] = $s;
	}

	public function setTimePeriodQualifier($s)
	{
		$this->collection[6] = $s;
	}

	public function setMonetaryAmount($s)
	{
		$this->collection[7] = $s;
	}

	public function setPercentageAsDecimal($s)
	{
		$this->collection[8] = $s;
	}

	public function setQuantityQualifier($s)
	{
		$this->collection[9] = $s;
	}

	public function setQuantity($s)
	{
		$this->collection[10] = $s;
	}

	public function setYesNoConditionOrResponseCode($s)
	{
		$this->collection[11] = $s;
	}

	public function setYesNoConditionOrResponseCode2($s)
	{
		$this->collection[12] = $s;
	}

	public function setCompositeMedicalProcedureIdentifier($s)
	{
		$this->collection[13] = $s;
	}

	public function setCompositeDiagnosisCodePointer($s)
	{
		$this->collection[14] = $s;
	}

	public function toArray()
	{
		return [
			'eligibilityOrBenefitInformationCode' => $this->getEligibilityOrBenefitInformationCode(),
			'coverageLevelCode' => $this->getCoverageLevelCode(),
			'serviceTypeCode' => $this->getServiceTypeCode(),
			'insuranceTypeCode' => $this->getInsuranceTypeCode(),
			'planCoverageDescription' => $this->getPlanCoverageDescription(),
			'timePeriodQualifier' => $this->getTimePeriodQualifier(),
			'monetaryAmount' => $this->getMonetaryAmount(),
			'percentageAsDecimal' => $this->getPercentageAsDecimal(),
			'quantityQualifier' => $this->getQuantityQualifier(),
			'quantity' => $this->getQuantity(),
			'yesNoConditionOrResponseCode' => $this->getYesNoConditionOrResponseCode(),
			'yesNoConditionOrResponseCode2' => $this->getYesNoConditionOrResponseCode2(),
			'compositeMedicalProcedureIdentifier' => $this->getCompositeMedicalProcedureIdentifier(),
			'compositeDiagnosisCodePointer' => $this->getCompositeDiagnosisCodePointer(),
		];
	}
}