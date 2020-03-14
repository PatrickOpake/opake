<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class PER extends Segment
{
	const FIELD_SIZE = 9;
	const NAME = 'PER';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getContactFunctionCode()
	{
		return $this->collection[1];
	}

	public function getName()
	{
		return $this->collection[2];
	}

	public function getCommunicationNumberQualifier1()
	{
		return $this->collection[3];
	}

	public function getCommunicationNumber1()
	{
		return $this->collection[4];
	}

	public function getCommunicationNumberQualifier2()
	{
		return $this->collection[5];
	}

	public function getCommunicationNumber2()
	{
		return $this->collection[6];
	}

	public function getCommunicationNumberQualifier3()
	{
		return $this->collection[7];
	}

	public function getCommunicationNumber3()
	{
		return $this->collection[8];
	}

	public function getContactInquiryReference()
	{
		return $this->collection[9];
	}

	public function setContactFunctionCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setName($s)
	{
		$this->collection[2] = $s;
	}

	public function setCommunicationNumberQualifier1($s)
	{
		$this->collection[3] = $s;
	}

	public function setCommunicationNumber1($s)
	{
		$this->collection[4] = $s;
	}

	public function setCommunicationNumberQualifier2($s)
	{
		$this->collection[5] = $s;
	}

	public function setCommunicationNumber2($s)
	{
		$this->collection[6] = $s;
	}

	public function setCommunicationNumberQualifier3($s)
	{
		$this->collection[7] = $s;
	}

	public function setCommunicationNumber3($s)
	{
		$this->collection[8] = $s;
	}

	public function setContactInquiryReference($s)
	{
		$this->collection[9] = $s;
	}

	public function toArray()
	{
		return [
			'contact_function_code' => $this->getContactFunctionCode(),
			'name' => $this->getName(),
			'communication_number_qualifier1' => $this->getCommunicationNumberQualifier1(),
			'communication_number1' => $this->getCommunicationNumber1(),
			'communication_number_qualifier2' => $this->getCommunicationNumberQualifier2(),
			'communication_number2' => $this->getCommunicationNumber2(),
			'communication_number_qualifier3' => $this->getCommunicationNumberQualifier3(),
			'communication_number3' => $this->getCommunicationNumber3(),
			'contact_inquiry_reference' => $this->getContactInquiryReference(),
		];
	}
}