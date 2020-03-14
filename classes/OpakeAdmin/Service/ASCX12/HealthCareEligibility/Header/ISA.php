<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class ISA extends Segment
{
	const FIELD_SIZE = 16;
	const NAME = 'ISA';

	public function __construct($c = '')
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getAuthInfoQualifier()
	{
		return $this->collection[1];
	}

	public function getAuthInformation()
	{
		return $this->collection[2];
	}

	public function getSecurityInfoQualifier()
	{
		return $this->collection[3];
	}

	public function getSecurityInformation()
	{
		return $this->collection[4];
	}

	public function getInterchangeIDQualifierSender()
	{
		return $this->collection[5];
	}

	public function getInterchangeSenderID()
	{
		return $this->collection[6];
	}

	public function getInterchangeIDQualifierReceiver()
	{
		return $this->collection[7];
	}

	public function getInterchangeReceiverID()
	{
		return $this->collection[8];
	}

	public function getInterchangeDate()
	{
		return $this->collection[9];
	}

	public function getInterchangeTime()
	{
		return $this->collection[10];
	}

	public function getRepetitionSeparator()
	{
		return $this->collection[11];
	}

	public function getInterchangeControlVersionNumber()
	{
		return $this->collection[12];
	}

	public function getInterchangeControlNumber()
	{
		return $this->collection[13];
	}

	public function getAcknowledgmentRequested()
	{
		return $this->collection[14];
	}

	public function getUsageIndicator()
	{
		return $this->collection[15];
	}

	public function getComponentElementSeparator()
	{
		return $this->collection[16];
	}

	public function setAuthInfoQualifier($s)
	{
		$this->collection[1] = $s;
	}

	public function setAuthInformation($s)
	{
		$this->collection[2] = $s;
	}

	public function setSecurityInfoQualifier($s)
	{
		$this->collection[3] = $s;
	}

	public function setSecurityInformation($s)
	{
		$this->collection[4] = $s;
	}

	public function setInterchangeIDQualifierSender($s)
	{
		$this->collection[5] = $s;
	}

	public function setInterchangeSenderID($s)
	{
		$this->collection[6] = $s;
	}

	public function setInterchangeIDQualifierReceiver($s)
	{
		$this->collection[7] = $s;
	}

	public function setInterchangeReceiverID($s)
	{
		$this->collection[8] = $s;
	}

	public function setInterchangeDate($s)
	{
		$this->collection[9] = $s;
	}

	public function setInterchangeTime($s)
	{
		$this->collection[10] = $s;
	}

	public function setRepetitionSeparator($s)
	{
		$this->collection[11] = $s;
	}

	public function setInterchangeControlVersionNumber($s)
	{
		$this->collection[12] = $s;
	}

	public function setInterchangeControlNumber($s)
	{
		$this->collection[13] = $s;
	}

	public function setAcknowledgmentRequested($s)
	{
		$this->collection[14] = $s;
	}

	public function setUsageIndicator($s)
	{
		$this->collection[15] = $s;
	}

	public function setComponentElementSeparator($s)
	{
		$this->collection[16] = $s;
	}

}