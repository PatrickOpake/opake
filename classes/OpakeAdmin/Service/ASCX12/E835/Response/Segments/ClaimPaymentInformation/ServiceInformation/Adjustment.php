<?php

namespace OpakeAdmin\Service\ASCX12\E835\Response\Segments\ClaimPaymentInformation\ServiceInformation;

class Adjustment
{

	const REASON_CODE_DEDUCTIBLE = 1;
	const REASON_CODE_CO_INS = 2;
	const REASON_CODE_CO_PAY = 3;

	/**
	 * @var int
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $reasonCode;

	/**
	 * @var float
	 */
	protected $amount;

	/**
	 * @var int
	 */
	protected $quantity;

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getReasonCode()
	{
		return $this->reasonCode;
	}

	/**
	 * @param string $reasonCode
	 */
	public function setReasonCode($reasonCode)
	{
		$this->reasonCode = $reasonCode;
	}

	/**
	 * @return float
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param float $amount
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;
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
	 * @return bool
	 */
	public function hasQuantity()
	{
		return ($this->quantity !== null);
	}

	public static function convertTypeFromString($stringType)
	{
		$types = [
			'CO' => \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::TYPE_CONTRACTUAL_OBLIGATIONS,
		    'OA' => \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::TYPE_OTHER_ADJUSTMENTS,
		    'PI' => \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::TYPE_PAYOR_INITIATED_REDUCTIONS,
		    'PR' => \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::TYPE_PATIENT_RESPONSIBILITY
		];

		if (isset($types[$stringType])) {
			return $types[$stringType];
		}

		return null;
	}
}
