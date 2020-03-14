<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class SE extends Segment
{
	const FIELD_SIZE = 2;
	const NAME = 'SE';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getTransactionSegmentCount()
	{
		return $this->collection[1];
	}

	public function getTransactionSetControlNumber()
	{
		return $this->collection[2];
	}

	public function setTransactionSegmentCount($s)
	{
		$this->collection[1] = $s;
	}

	public function setTransactionSetControlNumber($s)
	{
		$this->collection[2] = $s;
	}


}