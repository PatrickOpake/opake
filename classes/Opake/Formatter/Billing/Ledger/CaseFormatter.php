<?php

namespace Opake\Formatter\Billing\Ledger;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Insurance\AbstractType;

class CaseFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'dos',
				'total_charges',
				'financial_doc_count',
				'notes_count',
				'patient_insurances',
			    'procedures',
			    'is_self_pay_insurance',
			    'interests'
			],
			'fieldMethods' => [
				'id' => 'int',
				'dos' => 'dos',
			    'total_charges' => 'totalCharges',
			    'procedures' => 'procedures',
			    'financial_doc_count' => 'financialDocCount',
			    'notes_count' => 'notesCount',
				'patient_insurances' => 'patientInsurances',
			    'is_self_pay_insurance' => 'isSelfPayInsurance',
			    'interests' => 'interests'
			]
		]);
	}

	protected function formatDos($name, $options, \Opake\Model\Cases\Item $model)
	{
		$value = $model->time_start;
		$date = TimeFormat::fromDBDatetime($value);
		if ($date) {
			return TimeFormat::getDate($date);
		}

		return null;
	}

	protected function formatTotalCharges($name, $options, \Opake\Model\Cases\Item $model)
	{
		$total = 0.0;
		if ($model->coding->loaded()) {
			foreach ($model->coding->bills->find_all() as $bill) {
				if ($bill->amount) {
					$total += $bill->amount;
				}
			}
		}

		return $total;
	}

	protected function formatProcedures($name, $options, \Opake\Model\Cases\Item $model)
	{
		$procedures = [];
		if ($model->coding->loaded()) {
			foreach ($model->coding->bills->find_all() as $bill) {
				$procedures[] = $bill->getFormatter('LedgerListEntry')->toArray();
			}
		}

		return $procedures;
	}

	protected function formatFinancialDocCount($name, $options, \Opake\Model\Cases\Item $model)
	{
		return (int) $model->getFinancialDocuments()->count_all();
	}

	protected function formatNotesCount($name, $options, \Opake\Model\Cases\Item $model)
	{
		return (int) $model->getBillingNotesCount();
	}

	protected function formatPatientInsurances($name, $options, \Opake\Model\Cases\Item $model)
	{
		$result = [];
		$selectedInsurances = $model->registration->getSelectedInsurances();
		foreach ($selectedInsurances as $insurance) {
			$result[$insurance->selected_insurance_id] = $insurance->order;
		}

		return $result;
	}

	protected function formatIsSelfPayInsurance($name, $options, \Opake\Model\Cases\Item $model)
	{
		$caseCoding = $model->coding;
		if ($caseCoding->loaded()) {
			$assignedInsurance = $caseCoding->getAssignedInsurance();
			if ($assignedInsurance) {
				$caseInsurance = $assignedInsurance->getCaseInsurance();
				if ($caseInsurance->type == AbstractType::INSURANCE_TYPE_LOP || $caseInsurance->type == AbstractType::INSURANCE_TYPE_SELF_PAY) {
					return true;
				}
			}

		}

		return false;
	}

	protected function formatInterests($name, $options, \Opake\Model\Cases\Item $model)
	{
		$result = [];
		foreach ($model->ledger_interest_payments->order_by('id', 'desc')->find_all() as $ledgerInterestPayment) {
			$result[] = $ledgerInterestPayment->getFormatter('ListEntry')->toArray();
		}

		return $result;
	}
}