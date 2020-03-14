<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class BHT extends Segment
{
	const FIELD_SIZE = 6;
	const NAME = 'BHT';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getHierarchicalStructureCode()
	{
		return $this->collection[1];
	}

	public function getTransactionSetPurposeCode()
	{
		return $this->collection[2];
	}

	public function getReferenceIdentification()
	{
		return $this->collection[3];
	}

	public function getDate()
	{
		return $this->collection[4];
	}

	public function getTime()
	{
		return $this->collection[5];
	}

	public function getTransactionTypeCode()
	{
		return $this->collection[6];
	}

	public function setHierarchicalStructureCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setTransactionSetPurposeCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setReferenceIdentification($s)
	{
		$this->collection[3] = $s;
	}

	public function setDate($s)
	{
		$this->collection[4] = $s;
	}

	public function setTime($s)
	{
		$this->collection[5] = $s;
	}

	public function setTransactionTypeCode($s)
	{
		$this->collection[6] = $s;
	}

	public function toArray()
	{
		return [
			'hierarchicalStructureCode' => $this->getHierarchicalStructureCode(),
			'transactionSetPurposeCode' => $this->getTransactionSetPurposeCode(),
			'referenceIdentification' => $this->getReferenceIdentification(),
			'date' => $this->getDate(),
			'time' => $this->getTime(),
			'transactionTypeCode' => $this->getTransactionTypeCode(),
		];
	}
}