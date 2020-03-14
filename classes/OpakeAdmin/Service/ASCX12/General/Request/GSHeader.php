<?php

namespace OpakeAdmin\Service\ASCX12\General\Request;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class GSHeader extends AbstractRequestSegment
{
	protected $segmentDefinition = 'GS';

	protected $endSegmentDefinition = 'GE';

	/**
	 * @var string
	 */
	protected $groupControlNumber;

	/**
	 * @var string
	 */
	protected $functionalIdCode;

	/**
	 * @var \DateTime
	 */
	protected $requestDateTime;

	/**
	 * GSHeader constructor.
	 * @param string $functionalIdCode
	 * @param \DateTime $requestDateTime
	 */
	public function __construct(\DateTime $requestDateTime, $functionalIdCode)
	{
		$this->functionalIdCode = $functionalIdCode;
		$this->requestDateTime = $requestDateTime;
	}

	/**
	 * @return string
	 */
	public function getFunctionalIdCode()
	{
		return $this->functionalIdCode;
	}

	/**
	 * @param string $functionalIdCode
	 */
	public function setFunctionalIdCode($functionalIdCode)
	{
		$this->functionalIdCode = $functionalIdCode;
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
		if (!$this->functionalIdCode) {
			throw new \Exception ('Functional ID code is not assigned in segment GS');
		}

		if (!$this->requestDateTime) {
			throw new \Exception('Date time is required for GS header');
		}

		$requestDate = $this->requestDateTime->format('Ymd');
		$requestTime = $this->requestDateTime->format('Hi');

		$this->groupControlNumber = '1';

		$data[] = [
			$this->segmentDefinition,
		    $this->functionalIdCode,
		    'OPAKE',
		    'NAVICURE',
			$requestDate,
		    $requestTime,
		    $this->groupControlNumber,
		    'X',
		    $this->getVersion()
		];

		return $data;
	}

	/**
	 * @param $data
	 * @return array
	 */
	protected function generateSegmentsAfterChildren($data)
	{
		$data[] = [
			$this->endSegmentDefinition,
			$this->getCountOfChildSegments(),
			$this->groupControlNumber
		];

		return $data;
	}

	/**
	 * @return string
	 */
	protected function getVersion()
	{
		return '005010X222A1';
	}
}