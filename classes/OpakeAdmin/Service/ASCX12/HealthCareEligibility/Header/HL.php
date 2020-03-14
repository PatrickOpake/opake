<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class HL extends Segment
{
	const FIELD_SIZE = 4;
	const NAME = 'HL';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);

	}

	public function getHierarchicalIDNumber()
	{
		return $this->collection[1];
	}

	public function getHierarchicalParentIDNumber()
	{
		return $this->collection[2];
	}

	public function getHierarchicalLevelCode()
	{
		return $this->collection[3];
	}

	public function getHierarchicalChildCode()
	{
		return $this->collection[4];
	}

	public function setHierarchicalIDNumber($s)
	{
		$this->collection[1] = $s;
	}

	public function setHierarchicalParentIDNumber($s)
	{
		$this->collection[2] = $s;
	}

	public function setHierarchicalLevelCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setHierarchicalChildCode($s)
	{
		$this->collection[4] = $s;
	}

	public function toArray()
	{
		return [
			'id' => $this->getHierarchicalIDNumber(),
			'parent_id' => $this->getHierarchicalParentIDNumber(),
			'level_code' => $this->getHierarchicalLevelCode(),
			'child_code' => $this->getHierarchicalChildCode(),
		];
	}

}