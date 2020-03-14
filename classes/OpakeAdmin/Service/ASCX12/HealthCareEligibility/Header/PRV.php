<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class PRV extends Segment
{
	const FIELD_SIZE = 6;
	const NAME = 'PRV';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getProviderCode()
	{
		return $this->collection[1];
	}

	public function getReferenceIdentificationQualifier()
	{
		return $this->collection[2];
	}

	public function getReferenceIdentification()
	{
		return $this->collection[3];
	}

	public function getStateOrProvinceCode()
	{
		return $this->collection[4];
	}

	public function getProviderSpecialtyInformation()
	{
		return $this->collection[5];
	}

	public function getProviderOrganizationCode()
	{
		return $this->collection[6];
	}

	public function setProviderCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setReferenceIdentificationQualifier($s)
	{
		$this->collection[2] = $s;
	}

	public function setReferenceIdentification($s)
	{
		$this->collection[3] = $s;
	}

	public function setStateOrProvinceCode($s)
	{
		$this->collection[4] = $s;
	}

	public function setProviderSpecialtyInformation($s)
	{
		$this->collection[5] = $s;
	}

	public function setProviderOrganizationCode($s)
	{
		$this->collection[6] = $s;
	}

	public function toArray()
	{
		return [
			'provider_code' => $this->getProviderCode(),
			'ref_id_qualifier' => $this->getReferenceIdentificationQualifier(),
			'ref_id' => $this->getReferenceIdentification(),
			'state' => $this->getStateOrProvinceCode(),
			'provider_specialty_info' => $this->getProviderSpecialtyInformation(),
			'org_code' => $this->getProviderOrganizationCode(),
		];
	}
}