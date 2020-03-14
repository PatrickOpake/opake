<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class DMG extends Segment
{
	const FIELD_SIZE = 11;
	const NAME = 'DMG';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getDateTimePeriodFormatQualifier()
	{
		return $this->collection[1];
	}

	public function getDateTimePeriod()
	{
		return $this->collection[2];
	}

	public function getGenderCode()
	{
		return $this->collection[3];
	}

	public function getMaritalStatusCode()
	{
		return $this->collection[4];
	}

	public function getCompositeRaceOrEthnicityInformation()
	{
		return $this->collection[5];
	}

	public function getCitizenshipStatusCode()
	{
		return $this->collection[6];
	}

	public function getCountryCode()
	{
		return $this->collection[7];
	}

	public function getBasisOfVerificationCode()
	{
		return $this->collection[8];
	}

	public function getQuantity()
	{
		return $this->collection[9];
	}

	public function getCodeListQualifierCode()
	{
		return $this->collection[10];
	}

	public function getIndustryCode()
	{
		return $this->collection[11];
	}

	public function setDateTimePeriodFormatQualifier($s)
	{
		$this->collection[1] = $s;
	}

	public function setDateTimePeriod($s)
	{
		$this->collection[2] = $s;
	}

	public function setGenderCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setMaritalStatusCode($s)
	{
		$this->collection[4] = $s;
	}

	public function setCompositeRaceOrEthnicityInformation($s)
	{
		$this->collection[5] = $s;
	}

	public function setCitizenshipStatusCode($s)
	{
		$this->collection[6] = $s;
	}

	public function setCountryCode($s)
	{
		$this->collection[7] = $s;
	}

	public function setBasisOfVerificationCode($s)
	{
		$this->collection[8] = $s;
	}

	public function setQuantity($s)
	{
		$this->collection[9] = $s;
	}

	public function setCodeListQualifierCode($s)
	{
		$this->collection[10] = $s;
	}

	public function setIndustryCode($s)
	{
		$this->collection[11] = $s;
	}

	public function toArray()
	{
		return [
			'date_time_period_format' => $this->getDateTimePeriodFormatQualifier(),
			'date_time_period' => $this->getDateTimePeriod(),
			'gender_code' => $this->getGenderCode(),
			'marital_status_code' => $this->getMaritalStatusCode(),
			'composite_race' => $this->getCompositeRaceOrEthnicityInformation(),
			'citizenship_status_code' => $this->getCitizenshipStatusCode(),
			'country_code' => $this->getCountryCode(),
			'verification_code' => $this->getBasisOfVerificationCode(),
			'quantity' => $this->getQuantity(),
			'code_list_qualifier_code' => $this->getCodeListQualifierCode(),
			'industry_code' => $this->getIndustryCode(),
		];
	}

}