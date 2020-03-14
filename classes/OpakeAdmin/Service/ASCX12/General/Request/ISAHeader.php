<?php

namespace OpakeAdmin\Service\ASCX12\General\Request;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class ISAHeader extends AbstractRequestSegment
{
	protected $segmentDefinition = 'ISA';

	protected $endSegmentDefinition = 'IEA';

	/**
	 * @var \DateTime
	 */
	protected $requestDateTime;

	/**
	 * @var string
	 */
	protected $interchangeControlNumber;

	/**
	 * @var bool
	 */
	protected $needToGenerateAcknowledge = true;

	/**
	 * @var string
	 */
	protected $componentSeparator = ':';

	/**
	 * ISAHeader constructor.
	 * @param \DateTime $requestDateTime
	 */
	public function __construct(\DateTime $requestDateTime)
	{
		$this->requestDateTime = $requestDateTime;
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
	 * @return string
	 */
	public function getInterchangeControlNumber()
	{
		return $this->interchangeControlNumber;
	}

	/**
	 * @param string $interchangeControlNumber
	 */
	public function setInterchangeControlNumber($interchangeControlNumber)
	{
		$this->interchangeControlNumber = $interchangeControlNumber;
	}

	/**
	 * @return boolean
	 */
	public function isNeedToGenerateAcknowledge()
	{
		return $this->needToGenerateAcknowledge;
	}

	/**
	 * @param boolean $needToGenerateAcknowledge
	 */
	public function setNeedToGenerateAcknowledge($needToGenerateAcknowledge)
	{
		$this->needToGenerateAcknowledge = $needToGenerateAcknowledge;
	}

	/**
	 * @return string
	 */
	public function getComponentSeparator()
	{
		return $this->componentSeparator;
	}

	/**
	 * @param string $componentSeparator
	 */
	public function setComponentSeparator($componentSeparator)
	{
		$this->componentSeparator = $componentSeparator;
	}

	protected function generateSegmentsBeforeChildren($data)
	{
		if (!$this->requestDateTime) {
			throw new \Exception('Date time is required for ISA header');
		}

		$requestDate = $this->requestDateTime->format('ymd');
		$requestTime = $this->requestDateTime->format('Hi');

		$this->interchangeControlNumber = '100100001';

		$data[] = [
			$this->segmentDefinition,
			'00',
			str_repeat(' ', 10),
			'00',
			str_repeat(' ', 10),
			'ZZ',
			str_repeat(' ', 15),
			'ZZ',
			'NAVICURE       ',
			$requestDate,
			$requestTime,
			'^',
			'00501',
			$this->interchangeControlNumber,
			$this->needToGenerateAcknowledge ? '1' : '0',
			'T',
			$this->componentSeparator
		];

		return $data;
	}

	protected function generateSegmentsAfterChildren($data)
	{
		$data[] = [
			$this->endSegmentDefinition,
			$this->getCountOfChildSegments(),
			$this->interchangeControlNumber
		];

		return $data;
	}
}