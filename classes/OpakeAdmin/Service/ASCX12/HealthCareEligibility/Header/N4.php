<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class N4 extends Segment
{
	const FIELD_SIZE = 7;
	const NAME = 'N4';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getCityName()
	{
		return $this->collection[1];
	}

	public function getStateOrProvinceCode()
	{
		return $this->collection[2];
	}

	public function getPostalCode()
	{
		return $this->collection[3];
	}

	public function getCountryCode()
	{
		return $this->collection[4];
	}

	public function getLocationQualifier()
	{
		return $this->collection[5];
	}

	public function getLocationIdentifier()
	{
		return $this->collection[6];
	}

	public function getCountrySubdivisionCode()
	{
		return $this->collection[7];
	}

	public function setCityName($s)
	{
		$this->collection[1] = $s;
	}

	public function setStateOrProvinceCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setPostalCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setCountryCode($s)
	{
		$this->collection[4] = $s;
	}

	public function setLocationQualifier($s)
	{
		$this->collection[5] = $s;
	}

	public function setLocationIdentifier($s)
	{
		$this->collection[6] = $s;
	}

	public function setCountrySubdivisionCode($s)
	{
		$this->collection[7] = $s;
	}

	public function toArray()
	{
		return [
			'city_name' => $this->getCityName(),
			'state' => $this->getStateOrProvinceCode(),
			'postal' => $this->getPostalCode(),
			'country_code' => $this->getCountryCode(),
			'location_qualifier' => $this->getLocationQualifier(),
			'location_id' => $this->getLocationIdentifier(),
			'country_subdivision_code' => $this->getCountrySubdivisionCode(),
		];
	}
}