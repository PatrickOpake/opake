<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class TRN extends Segment
{
	const FIELD_SIZE = 4;
	const NAME = 'TRN';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getTraceTypeCode()
	{
		return $this->collection[1];
	}

	public function getReferenceIdentification()
	{
		return $this->collection[2];
	}
	
	public function getOriginatingCompanyIdentifier()
	{
		return $this->collection[3];
	}
	
	public function getReferenceIdentification2()
	{
		return $this->collection[4];
	}
	
	public function setTraceTypeCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setReferenceIdentification($s)
	{
		$this->collection[2] = $s;
	}

	public function setOriginatingCompanyIdentifier($s)
	{
		$this->collection[3] = $s;
	}

	public function setReferenceIdentification2($s)
	{
		$this->collection[4] = $s;
	}

	public function toArray()
	{
		return [
			'trace_type_code' => $this->getTraceTypeCode(),
			'ref_id' => $this->getReferenceIdentification(),
			'company_id' => $this->getOriginatingCompanyIdentifier(),
			'ref_id_2' => $this->getReferenceIdentification2(),
		];
	}
}