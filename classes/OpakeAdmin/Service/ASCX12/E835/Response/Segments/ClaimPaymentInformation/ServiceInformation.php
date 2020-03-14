<?php

namespace OpakeAdmin\Service\ASCX12\E835\Response\Segments\ClaimPaymentInformation;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;
use OpakeAdmin\Service\ASCX12\E835\Response\Segments\ClaimPaymentInformation\ServiceInformation\Adjustment;

class ServiceInformation extends AbstractResponseSegment
{
	protected $serviceHcpcsCode;

	protected $chargeAmount;

	protected $paidAmount;

	protected $quantity = 0;

	protected $allowedAmount;

	/**
	 * @var Adjustment[]
	 */
	protected $adjustments;

	/**
	 * @return mixed
	 */
	public function getServiceHcpcsCode()
	{
		return $this->serviceHcpcsCode;
	}

	/**
	 * @return mixed
	 */
	public function getChargeAmount()
	{
		return $this->chargeAmount;
	}

	/**
	 * @return mixed
	 */
	public function getPaidAmount()
	{
		return $this->paidAmount;
	}

	/**
	 * @return ServiceInformation\Adjustment[]
	 */
	public function getAdjustments()
	{
		return $this->adjustments;
	}

	/**
	 * @return int
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * @param int $quantity
	 */
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}

	/**
	 * @return mixed
	 */
	public function getAllowedAmount()
	{
		return $this->allowedAmount;
	}

	/**
	 * @param $data
	 */
	public function parseNodes($data)
	{
		foreach ($data as $line) {
			if ($line[0] === 'SVC') {
				$serviceDesc = $this->explodeComponents($line[1]);
				$this->serviceHcpcsCode = $serviceDesc[1];
				$this->chargeAmount = $line[2];
				$this->paidAmount = $line[3];
				if (!empty($line[5])) {
					$this->quantity = $line[5];
				}
			}
			if ($line[0] === 'CAS') {
				$type = Adjustment::convertTypeFromString($line[1]);
				for ($i = 2; $i <= 17; $i = $i+3) {
					if (!isset($line[$i])) {
						break;
					}

					$adjustment = new Adjustment();
					$adjustment->setType($type);
					$adjustment->setReasonCode($line[$i]);
					if (isset($line[$i+1])) {
						$adjustment->setAmount((float) $line[$i+1]);
					}
					if (isset($line[$i+2]) && $line[$i+2] !== '') {
						$adjustment->setQuantity((int) $line[$i+2]);
					}

					$this->adjustments[] = $adjustment;
				}
				$this->adjustmentAmount = $line[3];
				$this->adjustmentReasonCode = $line[2];
			}
			if ($line[0] === 'AMT' && (isset($line[1]) && $line[1] === 'B6')) {
				$this->allowedAmount = (float) $line[2];
			}
		}
	}
}