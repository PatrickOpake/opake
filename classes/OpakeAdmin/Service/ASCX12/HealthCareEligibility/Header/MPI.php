<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class MPI extends Segment
{
	const FIELD_SIZE = 7;
	const NAME = 'MPI';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getInformationStatusCode()
	{
		return $this->collection[1];
	}

	public function getEmploymentStatusCode()
	{
		return $this->collection[2];
	}

	public function getGovernmentServiceAffiliationCode()
	{
		return $this->collection[3];
	}

	public function getDescription()
	{
		return $this->collection[4];
	}

	public function getMilitaryServiceRankCode()
	{
		return $this->collection[5];
	}

	public function getDateTimePeriodFormatQualifier()
	{
		return $this->collection[6];
	}

	public function getDateTimePeriod()
	{
		return $this->collection[7];
	}

	public function setInformationStatusCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setEmploymentStatusCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setGovernmentServiceAffiliationCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setDescription($s)
	{
		$this->collection[4] = $s;
	}

	public function setMilitaryServiceRankCode($s)
	{
		$this->collection[5] = $s;
	}

	public function setDateTimePeriodFormatQualifier($s)
	{
		$this->collection[6] = $s;
	}

	public function setDateTimePeriod($s)
	{
		$this->collection[7] = $s;
	}

	public function toArray()
	{
		return [
			'informationStatusCode' => $this->getInformationStatusCode(),
			'employmentStatusCode' => $this->getEmploymentStatusCode(),
			'governmentServiceAffiliationCode' => $this->getGovernmentServiceAffiliationCode(),
			'description' => $this->getDescription(),
			'militaryServiceRankCode' => $this->getMilitaryServiceRankCode(),
			'dateTimePeriodFormatQualifier' => $this->getDateTimePeriodFormatQualifier(),
			'dateTimePeriod' => $this->getDateTimePeriod(),
		];
	}
}