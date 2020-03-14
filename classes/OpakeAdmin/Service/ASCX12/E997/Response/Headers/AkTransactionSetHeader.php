<?php

namespace OpakeAdmin\Service\ASCX12\E997\Response\Headers;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class AkTransactionSetHeader extends AbstractResponseSegment
{

	protected $status;

	protected $errorCode;

	protected $transactionControlNumber;

	/**
	 * @return mixed
	 */
	public function getTransactionControlNumber()
	{
		return $this->transactionControlNumber;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
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
			if ($line[0] === 'AK2') {
				$this->fetchTransactionControlNumber($line);
			}
			if ($line[0] === 'AK5') {
				$this->fetchStatusAndErrorCode($line);
			}
		}
	}

	/**
	 * Called when method parseNodes() was called for all child nodes
	 */
	public function allNodesParsed()
	{

	}

	/**
	 * @return bool
	 */
	public function hasAnyAcceptedStatus()
	{
		return (in_array($this->status, ['A', 'E']));
	}

	/**
	 * @return bool
	 */
	public function isAcceptedWithErrors()
	{
		return $this->status == 'E';
	}

	/**
	 * @return bool
	 */
	public function hasAnyRejectedStatus()
	{
		return (in_array($this->status, ['M', 'R', 'W', 'X']));
	}

	/**
	 * @return bool
	 */
	public function isRejectedMacFailed()
	{
		return $this->status == 'M';
	}

	/**
	 * @return bool
	 */
	public function isRejectedAssurenceValidityTestFailed()
	{
		return $this->status == 'W';
	}

	/**
	 * @return bool
	 */
	public function isRejectedContentCantBeAnalyzed()
	{
		return $this->status == 'X';
	}

	/**
	 * @param array $line
	 */
	protected function fetchTransactionControlNumber($line)
	{
		if (isset($line[2])) {
			$this->transactionControlNumber = $line[2];
		}
	}

	/**
	 * @param array $line
	 */
	protected function fetchStatusAndErrorCode($line)
	{
		if (isset($line[1])) {
			$this->status = $line[1];
		}

		if (isset($line[2])) {
			$this->errorCode = $line[2];
		}
	}
}