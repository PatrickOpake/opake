<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class IEA extends Segment
{
	const FIELD_SIZE = 2;
	const NAME = 'IEA';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getNumberOfIncludedFunctionalGroups()
	{
		return $this->collection[1];
	}

	public function getInterchangeControlNumber()
	{
		return $this->collection[2];
	}

	public function setNumberOfIncludedFunctionalGroups($s)
	{
		$this->collection[1] = $s;
	}

	public function setInterchangeControlNumber($s)
	{
		$this->collection[2] = $s;
	}

}