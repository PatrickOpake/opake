<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class NM1 extends Segment
{
	const FIELD_SIZE = 12;
	const NAME = 'NM1';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getEntityIdentifierCode()
	{
		return $this->collection[1];
	}

	public function getEntityTypeQualifier()
	{
		return $this->collection[2];
	}

	public function getNameLastOrOrganizationName()
	{
		return $this->collection[3];
	}

	public function getNameFirst()
	{
		return $this->collection[4];
	}

	public function getNameMiddle()
	{
		return $this->collection[5];
	}

	public function getNamePrefix()
	{
		return $this->collection[6];
	}

	public function getNameSuffix()
	{
		return $this->collection[7];
	}

	public function getIdentificationCodeQualifier()
	{
		return $this->collection[8];
	}

	public function getIdentificationCode()
	{
		return $this->collection[9];
	}

	public function getEntityRelationshipCode()
	{
		return $this->collection[10];
	}

	public function getEntityIdentifierCodeNotUsed()
	{
		return $this->collection[11];
	}

	public function getNameLastOrOrganizationNameNotUsed()
	{
		return $this->collection[12];
	}

	public function setEntityIdentifierCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setEntityTypeQualifier($s)
	{
		$this->collection[2] = $s;
	}

	public function setNameLastOrOrganizationName($s)
	{
		$this->collection[3] = $s;
	}

	public function setNameFirst($s)
	{
		$this->collection[4] = $s;
	}

	public function setNameMiddle($s)
	{
		$this->collection[5] = $s;
	}

	public function setNamePrefix($s)
	{
		$this->collection[6] = $s;
	}

	public function setNameSuffix($s)
	{
		$this->collection[7] = $s;
	}

	public function setIdentificationCodeQualifier($s)
	{
		$this->collection[8] = $s;
	}

	public function setIdentificationCode($s)
	{
		$this->collection[9] = $s;
	}

	public function setEntityRelationshipCode($s)
	{
		$this->collection[10] = $s;
	}

	public function setEntityIdentifierCodeNotUsed($s)
	{
		$this->collection[11] = $s;
	}

	public function setNameLastOrOrganizationNameNotUsed($s)
	{
		$this->collection[12] = $s;
	}

	public function toArray()
	{
		return [
			'entity_id_code' => $this->getEntityIdentifierCode(),
			'entity_type_qualifier' => $this->getEntityTypeQualifier(),
			'last_name' => $this->getNameLastOrOrganizationName(),
			'first_name' => $this->getNameFirst(),
			'middle_name' => $this->getNameMiddle(),
			'prefix_name' => $this->getNamePrefix(),
			'suffix_name' => $this->getNameSuffix(),
			'id_code_qualifier' => $this->getIdentificationCodeQualifier(),
			'id_code' => $this->getIdentificationCode(),
			'entity_relationship_code' => $this->getEntityRelationshipCode(),
			'entity_id_code_not_used' => $this->getEntityIdentifierCodeNotUsed(),
			'org_name_not_used' => $this->getNameLastOrOrganizationNameNotUsed(),
		];
	}

}