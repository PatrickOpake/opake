<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class LS extends Segment
{
	const FIELD_SIZE = 1;
	const NAME = 'LS';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getLoopIdentifierCode()
	{
		return $this->collection[1];
	}

	public function setLoopIdentifierCode($s)
	{
		$this->collection[1] = $s;
	}

	public function toArray()
	{
		return [
			'loopIdentifierCode' => $this->getLoopIdentifierCode()
		];
	}
}