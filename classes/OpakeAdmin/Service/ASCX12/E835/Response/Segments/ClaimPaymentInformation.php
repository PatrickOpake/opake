<?php

namespace OpakeAdmin\Service\ASCX12\E835\Response\Segments;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class ClaimPaymentInformation extends AbstractResponseSegment
{

	protected $referenceIdentifier;

	protected $status;

	protected $totalChargeAmount;

	protected $totalPaidAmount;

	protected $patientResponsibilityAmount;

	/**
	 * @return mixed
	 */
	public function getReferenceIdentifier()
	{
		return $this->referenceIdentifier;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return bool
	 */
	public function isPaymentProcessed()
	{
		return (in_array($this->status, ['1', '2', '3', '19', '20', '21', '22', '23', '25']));
	}

	/**
	 * @return bool
	 */
	public function isPaymentDenied()
	{
		return $this->status == '4';
	}

	/**
	 * @return mixed
	 */
	public function getTotalChargeAmount()
	{
		return $this->totalChargeAmount;
	}

	/**
	 * @return mixed
	 */
	public function getTotalPaidAmount()
	{
		return $this->totalPaidAmount;
	}

	/**
	 * @return mixed
	 */
	public function getPatientResponsibilityAmount()
	{
		return $this->patientResponsibilityAmount;
	}

	/**
	 * @param $data
	 */
	public function parseNodes($data)
	{
		foreach ($data as $line) {
			if ($line[0] === 'CLP') {
				$this->referenceIdentifier = $line[1];
				$this->status = $line[2];
				$this->totalChargeAmount = $line[3];
				$this->totalPaidAmount = $line[4];
				$this->patientResponsibilityAmount = $line[5];
			}
		}
	}
}