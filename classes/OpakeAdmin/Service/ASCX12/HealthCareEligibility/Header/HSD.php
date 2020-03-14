<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Segment;

class HSD extends Segment
{
	const FIELD_SIZE = 8;
	const NAME = 'HSD';

	public function __construct($c)
	{
		parent::__construct($c, self::FIELD_SIZE, self::NAME);
	}

	public function getQuantityQualifier()
	{
		return $this->collection[1];
	}

	public function getQuantity()
	{
		return $this->collection[2];
	}

	public function getUnitOrBasisForMeasurementCode()
	{
		return $this->collection[3];
	}

	public function getSampleSelectionModulus()
	{
		return $this->collection[4];
	}

	public function getTimePeriodQualifier()
	{
		return $this->collection[5];
	}

	public function getNumberOfPeriods()
	{
		return $this->collection[6];
	}

	public function getShipDeliveryOrCalendarPatternCode()
	{
		return $this->collection[7];
	}

	public function getShipDeliveryPatternTimeCode()
	{
		return $this->collection[8];
	}

	public function setQuantityQualifier($s)
	{
		$this->collection[1] = $s;
	}

	public function setQuantity($s)
	{
		$this->collection[2] = $s;
	}

	public function setUnitOrBasisForMeasurementCode($s)
	{
		$this->collection[3] = $s;
	}

	public function setSampleSelectionModulus($s)
	{
		$this->collection[4] = $s;
	}

	public function setTimePeriodQualifier($s)
	{
		$this->collection[5] = $s;
	}

	public function setNumberOfPeriods($s)
	{
		$this->collection[6] = $s;
	}

	public function setShipDeliveryOrCalendarPatternCode($s)
	{
		$this->collection[7] = $s;
	}

	public function setShipDeliveryPatternTimeCode($s)
	{
		$this->collection[8] = $s;
	}

	public function toArray()
	{
		return [
			'quantityQualifier' => $this->getQuantityQualifier(),
			'quantity' => $this->getQuantity(),
			'unitOrBasisForMeasurementCode' => $this->getUnitOrBasisForMeasurementCode(),
			'sampleSelectionModulus' => $this->getSampleSelectionModulus(),
			'timePeriodQualifier' => $this->getTimePeriodQualifier(),
			'numberOfPeriods' => $this->getNumberOfPeriods(),
			'shipDeliveryOrCalendarPatternCode' => $this->getShipDeliveryOrCalendarPatternCode(),
			'shipDeliveryPatternTimeCode' => $this->getShipDeliveryPatternTimeCode(),
		];
	}

}