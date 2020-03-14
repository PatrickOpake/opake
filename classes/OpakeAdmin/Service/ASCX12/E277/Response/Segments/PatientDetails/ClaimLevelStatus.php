<?php
namespace OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class ClaimLevelStatus extends AbstractResponseSegment
{
	protected $date;

	protected $actionCode;

	protected $amount;

	protected $note;

	/**
	 * @return mixed
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * @return mixed
	 */
	public function getActionCode()
	{
		return $this->actionCode;
	}

	/**
	 * @return mixed
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @return mixed
	 */
	public function getNote()
	{
		return $this->note;
	}

	/**
	 * @return bool
	 */
	public function isAmountRejected()
	{
		return ($this->actionCode === 'U');
	}

	/**
	 * @return bool
	 */
	public function isAmountAccepted()
	{
		return ($this->actionCode === 'WQ');
	}

	public function parseNodes($data)
	{
		foreach ($data as $line) {
			$this->date = \DateTime::createFromFormat('Ymd', $line[2]);
			$this->actionCode = $line[3];
			$this->amount = $line[4];
			$this->note = (isset($line[12])) ? $line[12] : '';
		}
	}
}