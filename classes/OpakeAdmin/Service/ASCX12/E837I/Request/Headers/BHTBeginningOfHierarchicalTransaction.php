<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request\Headers;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class BHTBeginningOfHierarchicalTransaction extends AbstractRequestSegment
{
	protected $segmentDefinition = 'BHT';

	protected $referenceIdentification;

	/**
	 * @var \DateTime
	 */
	protected $requestDateTime;

	/**
	 * BHTBeginningOfHierarchicalTransaction constructor.
	 * @param $referenceIdentification
	 * @param \DateTime $requestDateTime
	 */
	public function __construct(\DateTime $requestDateTime, $referenceIdentification)
	{
		$this->referenceIdentification = $referenceIdentification;
		$this->requestDateTime = $requestDateTime;
	}


	/**
	 * @return mixed
	 */
	public function getReferenceIdentification()
	{
		return $this->referenceIdentification;
	}

	/**
	 * @param mixed $referenceIdentification
	 */
	public function setReferenceIdentification($referenceIdentification)
	{
		$this->referenceIdentification = $referenceIdentification;
	}

	/**
	 * @return \DateTime
	 */
	public function getRequestDateTime()
	{
		return $this->requestDateTime;
	}

	/**
	 * @param \DateTime $requestDateTime
	 */
	public function setRequestDateTime($requestDateTime)
	{
		$this->requestDateTime = $requestDateTime;
	}

	/**
	 * @param $data
	 * @return array
	 * @throws \Exception
	 */
	protected function generateSegmentsBeforeChildren($data)
	{
		if (!$this->referenceIdentification) {
			throw new \Exception('Reference identification is required in segment BHT');
		}

		if (!$this->requestDateTime) {
			throw new \Exception('Date time is required for ISA header');
		}

		$requestDate = $this->requestDateTime->format('Ymd');
		$requestTime = $this->requestDateTime->format('Hi');

		$data[] = [
			$this->segmentDefinition,
			'0019',
			'00',
			$this->prepareString($this->referenceIdentification, 30),
			$requestDate,
			$requestTime,
			'CH'
		];

		return $data;
	}
}