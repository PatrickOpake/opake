<?php

namespace OpakeAdmin\Service\ASCX12\E835\Response\Segments;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class FinancialInformation extends AbstractResponseSegment
{
	/**
	 * @var \DateTime
	 */
	protected $eftDate;

	/**
	 * @var string
	 */
	protected $eftNumber;

	/**
	 * @return \DateTime
	 */
	public function getEftDate()
	{
		return $this->eftDate;
	}

	/**
	 * @return string
	 */
	public function getEftNumber()
	{
		return $this->eftNumber;
	}

	/**
	 * @param $data
	 */
	public function parseNodes($data)
	{
		foreach ($data as $line) {
			if ($line[0] === 'BPR') {
				$this->eftDate = \DateTime::createFromFormat('Ymd', $line[16]);
			}

			if ($line[0] === 'TRN') {
				$this->eftNumber = $line[2];
			}
		}
	}
}