<?php

namespace OpakeAdmin\Helper\Billing\Ledger;

use Opake\Model\Billing\Ledger\PaymentInfo;

class CasePaymentTotalsCalculator
{

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct($case)
	{
		$this->case = $case;
	}

	public function calculateTotals()
	{
		$insurancePaymentsAmount = 0;
		$patientPaymentsAmount = 0;
		$adjustmentsAmount = 0;
		$writeOffsAmount = 0;
		$charges = 0;

		$caseCoding = $this->case->coding;
		if ($caseCoding->loaded()) {
			foreach ($caseCoding->bills->find_all() as $bill) {
				$calculator = new PaymentTotalsCalculator($this->case, $bill);
				$result = $calculator->calculateTotals();

				$insurancePaymentsAmount += $result['insurancePayments'];
				$patientPaymentsAmount += $result['patientPayments'];
				$adjustmentsAmount += $result['adjustments'];
				$writeOffsAmount += $result['writeOffs'];
				$charges += $result['charges'];
			}
		}

		foreach ($this->case->ledger_interest_payments->find_all() as $interestPayment) {
			$amount = (float) $interestPayment->amount;
			$charges += $amount;
			$insurancePaymentsAmount += $amount;
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