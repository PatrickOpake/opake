<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request\Headers;

use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;

class STTransactionSet extends AbstractRequestSegment
{
	protected $segmentDefinition = 'ST';

	protected $endSegmentDefinition = 'SE';

	/**
	 * @var string
	 */
	protected $transactionSetControlNumber;

	/**
	 * @param string $controlNumber
	 */
	public function __construct($controlNumber)
	{
		$this->transactionSetControlNumber = $controlNumber;
	}

	/**
	 * @param $data
	 * @return array
	 * @throws \Exception
	 */
	protected function generateSegmentsBeforeChildren($data)
	{
		$data[] = [
			$this->segmentDefinition,
			'837',
			$this->transactionSetControlNumber,
			'005010X223A1'
		];

		return $data;
	}

	protected function generateSegmentsAfterChildren($data)
	{
		$data[] = [
			$this->endSegmentDefinition,
			count($data) + 1,
			$this->transactionSetControlNumber
		];

		return $data;
	}
}