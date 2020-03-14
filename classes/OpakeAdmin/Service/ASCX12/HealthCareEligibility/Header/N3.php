<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class N3 extends Segment
{
	const FIELD_SIZE = 2;
	const NAME = 'N3';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getAddressInformation1()
	{
		return $this->collection[1];
	}

	public function getAddressInformation2()
	{
		return $this->collection[2];
	}

	public function setAddressInformation1($s)
	{
		$this->collection[1] = $s;
	}

	public function setAddressInformation2($s)
	{
		$this->collection[2] = $s;
	}

	public function toArray()
	{
		return [
			'address_info_1' => $this->getAddressInformation1(),
			'address_info_2' => $this->getAddressInformation2(),
		];
	}
}