<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class MSG extends Segment
{
	const FIELD_SIZE = 3;
	const NAME = 'MSG';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getFreeFormMessageText()
	{
		return $this->collection[1];
	}

	public function getPrinterCarriageControlCode()
	{
		return $this->collection[2];
	}

	public function getNumber()
	{
		return $this->collection[3];
	}

	public function setFreeFormMessageText($s)
	{
		$this->collection[1] = $s;
	}

	public function setPrinterCarriageControlCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setNumber($s)
	{
		$this->collection[3] = $s;
	}

	public function toArray()
	{
		return [
			'freeFormMessageText' => $this->getFreeFormMessageText(),
			'printerCarriageControlCode' => $this->getPrinterCarriageControlCode(),
			'number' => $this->getNumber(),
		];
	}
}