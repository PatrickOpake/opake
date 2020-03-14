<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class DTP extends Segment
{
	const FIELD_SIZE = 3;
	const NAME = 'DTP';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getDateTimeQualifier()
	{
		return $this->collection[1];
	}

	public function getDateTimePeriodFormatQualifier()
	{
		return $this->collection[2];
	}

	public function getDateTimePeriod()
	{
		return $this->collection[3];
	}

	public function setDateTimeQualifier($s)
	{
		$this->collection[1] = $s;
	}

	public function setDateTimePeriodFormatQualifier($s)
	{
		$this->collection[2] = $s;
	}

	public function setDateTimePeriod($s)
	{
		$this->collection[3] = $s;
	}

	public function toArray()
	{
		return [
			'dateTimeQualifier' => $this->getDateTimeQualifier(),
			'dateTimePeriodFormatQualifier' => $this->getDateTimePeriodFormatQualifier(),
			'dateTimePeriod' => $this->getDateTimePeriod(),
		];
	}
}