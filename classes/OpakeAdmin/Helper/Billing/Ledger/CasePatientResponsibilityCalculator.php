<?php

namespace OpakeAdmin\Helper\Billing\Ledger;

class CasePatientResponsibilityCalculator
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

	public function calculateResponsibilityDetails()
	{
		$totalAmounts = [
			'insurance' => 0.00,
			'coPay' => 0.00,
			'coIns' => 0.00,
			'deductible' => 0.00,
			'oop' => 0.00,
		];

		$casePatient = $this->case->registration->patient;
		$caseCoding = $this->case->coding;
		if ($caseCoding->loaded()) {
			foreach ($caseCoding->bills->find_all() as $bill) {
				$calculator = new PatientResponsibilityCalculator($casePatient, $this->case, $bill);
				$result = $calculator->calculateResponsibilityDetails();

				$totalAmounts['insurance'] += $result['insurance'];
				$totalAmounts['coPay'] += $result['coPay'];
				$totalAmounts['coIns'] += $result['coIns'];
				$totalAmounts['deductible'] += $result['deductible'];
				$totalAmounts['oop'] += $result['oop'];
			}
		}

		return $totalAmounts;
	}
}