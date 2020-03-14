<?php

namespace OpakeAdmin\Helper\Billing\Ledger;

use Opake\Model\Billing\Ledger\PaymentInfo;
use Opake\Model\Insurance\AbstractType;

class PatientResponsibilityCalculator
{
	/**
	 * Cache not to determine twice if patient has insurance responsibility
	 * @var array
	 */
	protected static $patientHasInsurances = [];

	/**
	 * @var \Opake\Model\Patient
	 */
	protected $patient;

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
	 * @param \Opake\Model\Patient $patient
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Cases\Coding\Bill $bill
	 */
	public function __construct($patient, $case, $bill)
	{
		$this->patient = $patient;
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

	/**
	 * @return array
	 */
	public function calculateResponsibilityDetails()
	{
		$patientId = $this->patient->id();
		if (!isset(static::$patientHasInsurances[$patientId])) {
			static::$patientHasInsurances[$patientId] = ($this->patient->insurances->count_all() > 0);
		}

		$patientHasInsurances = static::$patientHasInsurances[$patientId];

		$procedureResponsibilityAmounts = [
			'insurance' => 0.00,
			'coPay' => 0.00,
			'coIns' => 0.00,
			'deductible' => 0.00
		];

		$appliedPayments = $this->appliedPayments;

		if ($appliedPayments === null) {
			$appliedPayments = $this->bill->applied_payments
				->find_all()
				->as_array();
		}

		$balance = (float) $this->bill->amount;
		foreach ($appliedPayments as $payment) {
			$balance -= $payment->amount;
		}

		$hasInsurancePaymentWithResp = false;
		$caseCoding = $this->case->coding;

		foreach ($appliedPayments as $payment) {

			if ($payment->resp_co_pay_amount) {
				$procedureResponsibilityAmounts['coPay'] += (float) $payment->resp_co_pay_amount;
			}
			if ($payment->resp_co_ins_amount) {
				$procedureResponsibilityAmounts['coIns'] += (float) $payment->resp_co_ins_amount;
			}
			if ($payment->resp_deduct_amount) {
				$procedureResponsibilityAmounts['deductible'] += (float) $payment->resp_deduct_amount;
			}

			if ($payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_INSURANCE && (
					$procedureResponsibilityAmounts['coPay'] > 0 ||
					$procedureResponsibilityAmounts['coIns'] > 0 ||
					$procedureResponsibilityAmounts['deductible'] > 0
				)) {
				$hasInsurancePaymentWithResp = true;
			}

			if ($payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_PAY ||
				$payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_PAY) {
				$procedureResponsibilityAmounts['coPay'] -= (float) $payment->amount;
			} else if ($payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_CO_INSURANCE ||
				$payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_CO_INSURANCE) {
				$procedureResponsibilityAmounts['coIns'] -= (float) $payment->amount;
			} else if ($payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_PATIENT_DEDUCTIBLE ||
				$payment->payment_info->payment_source == PaymentInfo::PAYMENT_SOURCE_WRITE_OFF_DEDUCTIBLE) {
				$procedureResponsibilityAmounts['deductible'] -= (float) $payment->amount;
			}
		}

		if ($patientHasInsurances && !$hasInsurancePaymentWithResp) {
			$procedureResponsibilityAmounts['insurance'] = $balance;
		}

		if ($this->hasForcePatientResp()) {
			$procedureResponsibilityAmounts['insurance'] = 0.00;
			$procedureResponsibilityAmounts['oop'] = $balance;
			return $procedureResponsibilityAmounts;
		}

		if ($caseCoding->loaded()) {
			$assignedInsurance = $caseCoding->getAssignedInsurance();
			if ($assignedInsurance) {
				$caseInsurance = $assignedInsurance->getCaseInsurance();
				if ($caseInsurance->type == AbstractType::INSURANCE_TYPE_LOP || $caseInsurance->type == AbstractType::INSURANCE_TYPE_SELF_PAY) {
					$procedureResponsibilityAmounts['insurance'] = 0.00;
					$procedureResponsibilityAmounts['oop'] = $balance;
					return $procedureResponsibilityAmounts;
				}
			}

		}

		$procedureResponsibilityAmounts['oop'] = ($balance - (
				$procedureResponsibilityAmounts['insurance']
				+ $procedureResponsibilityAmounts['coPay']
				+ $procedureResponsibilityAmounts['coIns']
				+ $procedureResponsibilityAmounts['deductible']
			));


		return $procedureResponsibilityAmounts;
	}

	/**
	 * @return float
	 */
	public function calculateResponsibilityBalance()
	{
		$amounts = $this->calculateResponsibilityDetails();
		return ($amounts['coPay'] + $amounts['coIns'] + $amounts['deductible'] + $amounts['oop']);
	}

	protected function hasForcePatientResp()
	{

		$app = \Opake\Application::get();
		$applyingOptions = $app->orm->get('Billing_Ledger_ApplyingOptions')
			->where('coding_bill_id', $this->bill->id())
			->find();

		if ($applyingOptions->loaded()) {
			return (bool) $applyingOptions->is_force_patient_resp;
		}

		return false;
	}
}