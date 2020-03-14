<?php

namespace OpakeAdmin\Helper\Analytics\Reports\CasesReport\RowSource;

use OpakeAdmin\Helper\Billing\Ledger\CasePatientResponsibilityCalculator;
use OpakeAdmin\Helper\Billing\Ledger\CasePaymentTotalsCalculator;

class BaseCaseRowSource
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var array
	 */
	protected $caseAppliedPaymentsCache;

	/**
	 * @var array
	 */
	protected $caseTotalsCache;

	/**
	 * @var array
	 */
	protected $casePatientResponsibilityCache;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct($case)
	{
		$this->case = $case;
	}

	/**
	 * @return \Opake\Model\Cases\Item
	 */
	public function getCase()
	{
		return $this->case;
	}

	/**
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function setCase($case)
	{
		$this->case = $case;
	}

	/**
	 * @return array
	 */
	public function getCaseAppliedPayments()
	{
		if ($this->caseAppliedPaymentsCache === null) {
			$this->caseAppliedPaymentsCache = [];

			$coding = $this->case->coding;
			if ($coding->loaded()) {
				foreach ($coding->bills->find_all() as $bill) {
					foreach ($bill->applied_payments->find_all() as $payment) {
						$this->caseAppliedPaymentsCache[] = $payment;
					}
				}
			}
		}

		return $this->caseAppliedPaymentsCache;
	}

	public function getCaseTotals()
	{

		if (!$this->caseTotalsCache) {
			$calc = new CasePaymentTotalsCalculator($this->case);
			$this->caseTotalsCache = $calc->calculateTotals();
		}

		return $this->caseTotalsCache;
	}

	public function getCasePatientResponsibility()
	{
		if (!$this->casePatientResponsibilityCache) {
			$calc = new CasePatientResponsibilityCalculator($this->case);
			$this->casePatientResponsibilityCache = $calc->calculateResponsibilityDetails();
		}

		return $this->casePatientResponsibilityCache;
	}

}

