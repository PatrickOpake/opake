<?php

namespace OpakeAdmin\Service\ASCX12\E997\Response\Segments;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class AkImplNote extends AbstractResponseSegment
{
	protected $positionInSegment;

	protected $referenceNumber;

	protected $errorCode;

	/**
	 * @return mixed
	 */
	public function getPositionInSegment()
	{
		return $this->positionInSegment;
	}

	/**
	 * @return mixed
	 */
	public function getReferenceNumber()
	{
		return $this->referenceNumber;
	}

	/**
	 * @return mixed
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}

	/**
	 * Array of nodes to parse
	 * The method should extract values which are used in application and assign it
	 * to variables
	 *
	 * Example of input data:
	 * [
	 *  ['NM1', 00001, 'SUBMITTER'],
	 *  ['PER', 'New York, Submitter str. 10']
	 * ]
	 *
	 * @param $data
	 */
	public function parseNodes($data)
	{
		foreach ($data as $line) {
			if ($line[0] === 'AK4') {
				$this->fetchErrorIdentification($line);
			}
		}
	}

	/**
	 * Called when method parseNodes() was called for all child nodes
	 */
	public function allNodesParsed()
	{

	}

	protected function fetchErrorIdentification($line)
	{
		if (isset($line[1])) {
			$this->positionInSegment = $line[1];
		}
		if (isset($line[2])) {
			$this->referenceNumber = $line[2];
		}
		if (isset($line[3])) {
			$this->errorCode = $line[3];
		}
	}
}