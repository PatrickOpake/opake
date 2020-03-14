<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class REF extends Segment
{
	const FIELD_SIZE = 4;
	const NAME = 'REF';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getReferenceIdentificationQualifier()
	{
		return $this->collection[1];
	}

	public function getReferenceIdentification()
	{
		return $this->collection[2];
	}

	public function getDescription()
	{
		return $this->collection[3];
	}

	public function getReferenceIdentifier()
	{
		return $this->collection[4];
	}

	public function setReferenceIdentificationQualifier($s)
	{
		$this->collection[1] = $s;
	}

	public function setReferenceIdentification($s)
	{
		$this->collection[2] = $s;
	}

	public function setDescription($s)
	{
		$this->collection[3] = $s;
	}

	public function setReferenceIdentifier($s)
	{
		$this->collection[4] = $s;
	}

	public function toArray()
	{
		return [
			'ref_id_qualifier' => $this->getReferenceIdentificationQualifier(),
			'ref_id' => $this->getReferenceIdentification(),
			'description' => $this->getDescription(),
			'ref_identifier' => $this->getReferenceIdentifier(),
		];
	}
}