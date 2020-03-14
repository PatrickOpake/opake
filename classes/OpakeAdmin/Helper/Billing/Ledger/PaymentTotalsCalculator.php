<?php

namespace OpakeAdmin\Helper\Billing\Ledger;

use Opake\Model\Billing\Ledger\PaymentInfo;

class PaymentTotalsCalculator
{
	/**
	 * @var \Opake\Model\Cases\Coding\Bill
	 */
	protected $bill;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var \Opake\Model\Billing\Ledger\AppliedPayment[]
	 */
	protected $appliedPayments;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Cases\Coding\Bill $bill
	 */
	public function __construct($case, $bill)
	{
		$this->case = $case;
		$this->bill = $bill;
	}


	/**
	 * @return \Opake\Model\Billing\Ledger\AppliedPayment[]
	 */
	public function getAppliedPayments()
	{
		return $this->appliedPayments;
	}

	/**
	 * @param \Opake\Model\Billing\Ledger\AppliedPayment[] $appliedPayments
	 */
	public function setAppliedPayments($appliedPayments)
	{
		$this->appliedPayments = $appliedPayments;
	}

	public function calculateTotals()
	{
		$appliedPayments = $this->appliedPayments;

		if ($appliedPayments === null) {
			$appliedPayments = $this->bill->applied_payments
				->find_all()
				->as_array();
		}

		$insurancePaymentsAmount = 0;
		$patientPaymentsAmount = 0;
		$adjustmentsAmount = 0;
		$writeOffsAmount = 0;
		$charges = $this->bill->amount;

		foreach ($appliedPayments as $payment) {

			$paymentInfo = $payment->payment_info;

			if ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_INSURANCE) {
				$insurancePaymentsAmount += (float) $payment->amount;
			} else if (
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_PAY ||
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_INSURANCE ||
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_DEDUCTIBLE ||
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_OOP
			) {
				$patientPaymentsAmount += (float) $payment->amount;
			} else if ($paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT) {
				$adjustmentsAmount += (float) $payment->amount;
			} else if (
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF ||
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_PAY ||
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_INSURANCE ||
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_DEDUCTIBLE ||
				$paymentInfo->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_OOP
			) {
				$writeOffsAmount += (float) $payment->amount;
			}
		}

		$outstandingBalance = ($charges - ($insurancePaymentsAmount + $patientPaymentsAmount + $adjustmentsAmount + $writeOffsAmount));

		return [
			'charges' => $charges,
		    'insurancePayments' => $insurancePaymentsAmount,
		    'patientPayments' => $patientPaymentsAmount,
		    'adjustments' => $adjustmentsAmount,
		    'writeOffs' => $writeOffsAmount,
		    'outstandingBalance' => $outstandingBalance
		];
	}

}