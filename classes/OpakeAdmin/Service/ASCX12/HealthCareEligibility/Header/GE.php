<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class GE extends Segment
{
	const FIELD_SIZE = 2;
	const NAME = 'GE';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getNumberOfTransactionsSetsIncluded()
	{
		return $this->collection[1];
	}

	public function getGroupControlNumber()
	{
		return $this->collection[2];
	}

	public function setNumberOfTransactionsSetsIncluded($s)
	{
		$this->collection[1] = $s;
	}

	public function setGroupControlNumber($s)
	{
		$this->collection[2] = $s;
	}
}