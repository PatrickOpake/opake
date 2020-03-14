<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class GS extends Segment
{
	const FIELD_SIZE = 8;
	const NAME = 'GS';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getFunctionalIDCode()
	{
		return $this->collection[1];
	}

	public function getApplicationSendersCode()
	{
		return $this->collection[2];
	}

	public function getApplicationReceiversCode()
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

	public function getGroupControlNumber()
	{
		return $this->collection[6];
	}

	public function getResponsibleAgencyCode()
	{
		return $this->collection[7];
	}

	public function getVersionReleaseIndustryIDCode()
	{
		return $this->collection[8];
	}

	public function setFunctionalIDCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setApplicationSendersCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setApplicationReceiversCode($s)
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

	public function setGroupControlNumber($s)
	{
		$this->collection[6] = $s;
	}

	public function setResponsibleAgencyCode($s)
	{
		$this->collection[7] = $s;
	}

	public function setVersionReleaseIndustryIDCode($s)
	{
		$this->collection[8] = $s;
	}
}