<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class ST extends Segment
{
	const FIELD_SIZE = 3;
	const NAME = 'ST';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getTransactionSetIDCode()
	{
		return $this->collection[1];
	}

	public function getTransactionSetControlNumber()
	{
		return $this->collection[2];
	}

	public function getImplementationConventionReference()
	{
		return $this->collection[3];
	}

	public function setTransactionSetIDCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setTransactionSetControlNumber($s)
	{
		$this->collection[2] = $s;
	}

	public function setImplementationConventionReference($s)
	{
		$this->collection[3] = $s;
	}
}