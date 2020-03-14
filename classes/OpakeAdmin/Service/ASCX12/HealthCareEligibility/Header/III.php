<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class III extends Segment
{
	const FIELD_SIZE = 9;
	const NAME = 'III';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getCodeListQualifierCode()
	{
		return $this->collection[1];
	}

	public function getIndustryCode()
	{
		return $this->collection[2];
	}

	public function getCodeCategory()
	{
		return $this->collection[3];
	}

	public function getFreeFormMessageText()
	{
		return $this->collection[4];
	}

	public function getQuantity()
	{
		return $this->collection[5];
	}

	public function getCompositeUnitOfMeasure()
	{
		return $this->collection[6];
	}

	public function getSurfaceLayerPositionCode1()
	{
		return $this->collection[7];
	}

	public function getSurfaceLayerPositionCode2()
	{
		return $this->collection[8];
	}

	public function getSurfaceLayerPositionCode3()
	{
		return $this->collection[9];
	}

	public function setCodeListQualifierCode($s)
	{
		$this->collection[1] = $s;
	}

	public function setIndustryCode($s)
	{
		$this->collection[2] = $s;
	}

	public function setCodeCategory($s)
	{
		$this->collection[3] = $s;
	}

	public function setFreeFormMessageText($s)
	{
		$this->collection[4] = $s;
	}

	public function setQuantity($s)
	{
		$this->collection[5] = $s;
	}

	public function setCompositeUnitOfMeasure($s)
	{
		$this->collection[6] = $s;
	}

	public function setSurfaceLayerPositionCode1($s)
	{
		$this->collection[7] = $s;
	}

	public function setSurfaceLayerPositionCode2($s)
	{
		$this->collection[8] = $s;
	}

	public function setSurfaceLayerPositionCode3($s)
	{
		$this->collection[9] = $s;
	}

	public function toArray()
	{
		return [
			'codeListQualifierCode' => $this->getCodeListQualifierCode(),
			'industryCode' => $this->getIndustryCode(),
			'codeCategory' => $this->getCodeCategory(),
			'freeFormMessageText' => $this->getFreeFormMessageText(),
			'quantity' => $this->getQuantity(),
			'compositeUnitOfMeasure' => $this->getCompositeUnitOfMeasure(),
			'surfaceLayerPositionCode1' => $this->getSurfaceLayerPositionCode1(),
			'surfaceLayerPositionCode2' => $this->getSurfaceLayerPositionCode2(),
			'surfaceLayerPositionCode3' => $this->getSurfaceLayerPositionCode3(),
		];
	}
}