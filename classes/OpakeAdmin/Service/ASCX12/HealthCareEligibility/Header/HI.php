<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class HI extends Segment
{
	const FIELD_SIZE = 12;
	const NAME = 'HI';

	public function __construct($c = null)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getHealthCareCodeInformation1()
	{
		return $this->collection[1];
	}

	public function getHealthCareCodeInformation2()
	{
		return $this->collection[2];
	}

	public function getHealthCareCodeInformation3()
	{
		return $this->collection[3];
	}

	public function getHealthCareCodeInformation4()
	{
		return $this->collection[4];
	}

	public function getHealthCareCodeInformation5()
	{
		return $this->collection[5];
	}

	public function getHealthCareCodeInformation6()
	{
		return $this->collection[6];
	}

	public function getHealthCareCodeInformation7()
	{
		return $this->collection[7];
	}

	public function getHealthCareCodeInformation8()
	{
		return $this->collection[8];
	}

	public function getHealthCareCodeInformation9()
	{
		return $this->collection[9];
	}

	public function getHealthCareCodeInformation10()
	{
		return $this->collection[10];
	}

	public function getHealthCareCodeInformation11()
	{
		return $this->collection[11];
	}

	public function getHealthCareCodeInformation12()
	{
		return $this->collection[12];
	}

	public function setHealthCareCodeInformation1($s)
	{
		$this->collection[1] = $s;
	}

	public function setHealthCareCodeInformation2($s)
	{
		$this->collection[2] = $s;
	}

	public function setHealthCareCodeInformation3($s)
	{
		$this->collection[3] = $s;
	}

	public function setHealthCareCodeInformation4($s)
	{
		$this->collection[4] = $s;
	}

	public function setHealthCareCodeInformation5($s)
	{
		$this->collection[5] = $s;
	}

	public function setHealthCareCodeInformation6($s)
	{
		$this->collection[6] = $s;
	}

	public function setHealthCareCodeInformation7($s)
	{
		$this->collection[7] = $s;
	}

	public function setHealthCareCodeInformation8($s)
	{
		$this->collection[8] = $s;
	}

	public function setHealthCareCodeInformation9($s)
	{
		$this->collection[9] = $s;
	}

	public function setHealthCareCodeInformation10($s)
	{
		$this->collection[10] = $s;
	}

	public function setHealthCareCodeInformation11($s)
	{
		$this->collection[11] = $s;
	}

	public function setHealthCareCodeInformation12($s)
	{
		$this->collection[12] = $s;
	}

	public function toArray()
	{
		return [
			'healthCareCodeInformation1' => $this->getHealthCareCodeInformation1(),
			'healthCareCodeInformation2' => $this->getHealthCareCodeInformation2(),
			'healthCareCodeInformation3' => $this->getHealthCareCodeInformation3(),
			'healthCareCodeInformation4' => $this->getHealthCareCodeInformation4(),
			'healthCareCodeInformation5' => $this->getHealthCareCodeInformation5(),
			'healthCareCodeInformation6' => $this->getHealthCareCodeInformation6(),
			'healthCareCodeInformation7' => $this->getHealthCareCodeInformation7(),
			'healthCareCodeInformation8' => $this->getHealthCareCodeInformation8(),
			'healthCareCodeInformation9' => $this->getHealthCareCodeInformation9(),
			'healthCareCodeInformation10' => $this->getHealthCareCodeInformation10(),
			'healthCareCodeInformation11' => $this->getHealthCareCodeInformation11(),
			'healthCareCodeInformation12' => $this->getHealthCareCodeInformation12(),
		];
	}
}