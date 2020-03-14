<?php

namespace OpakeAdmin\Service\ASCX12\E997\Response\Segments;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class AkErrorId extends AbstractResponseSegment
{
	protected $errorSegmentDefinition;

	protected $errorSegmentPosition;

	protected $errorSegmentLoopId;

	protected $errorCode;

	/**
	 * @return mixed
	 */
	public function getErrorSegmentDefinition()
	{
		return $this->errorSegmentDefinition;
	}

	/**
	 * @return mixed
	 */
	public function getErrorSegmentPosition()
	{
		return $this->errorSegmentPosition;
	}

	/**
	 * @return mixed
	 */
	public function getErrorSegmentLoopId()
	{
		return $this->errorSegmentLoopId;
	}

	/**
	 * @return mixed
	 */
	public function getErrorCode()
	{
		return $this->errorCode;
	}

	/**
	 * @return string
	 */
	public function getErrorDescription()
	{
		$list = static::getLisfOfErrorDescriptions();
		return (isset($list[$this->errorCode])) ? $list[$this->errorCode] : $this->errorCode;
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
			if ($line[0] === 'AK3') {
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
			$this->errorSegmentDefinition = $line[1];
		}
		if (isset($line[2])) {
			$this->errorSegmentPosition = $line[2];
		}
		if (isset($line[3])) {
			$this->errorSegmentLoopId = $line[3];
		}
		if (isset($line[4])) {
			$this->errorCode = $line[4];
		}
	}

	/**
	 * @return array
	 */
	public static function getLisfOfErrorDescriptions()
	{
		return [
			'1' => 'Unrecognized segment ID',
		    '2' => 'Unexpected segment',
		    '3' => 'Required Segment Missing',
		    '4' => 'Loop Occurs Over Maximum Times',
		    '5' => 'Segment Exceeds Maximum Use',
		    '6' => 'Segment Not in Defined Transaction Set',
		    '7' => 'Segment Not in Proper Sequence',
		    '8' => 'Segment Has Data Element Errors',
		    'I4' => 'Implementation “Not Used” Segment Present',
		    'I6' => 'Implementation Dependent Segment Missing',
		    'I7' => 'Implementation Loop Occurs Under Minimum Times',
		    'I8' => 'Implementation Segment Below Minimum Use',
		    'I9' => 'Implementation Dependent “Not Used” Segment Present'
		];
	}
}