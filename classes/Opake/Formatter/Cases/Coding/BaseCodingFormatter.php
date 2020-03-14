<?php

namespace Opake\Formatter\Cases\Coding;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\Cases\Coding;
use Opake\Model\DischargeStatusCode;

class BaseCodingFormatter extends BaseDataFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'case_id',
				'first_surgeon',
				'authorization_release_information_payment',
				'bill_type',
				'reference_number',
				'has_lab_services_outside',
				'lab_services_outside_amount',
				'insurance_order',
				'amount_paid_by_other_insurance',
				'amount_paid',
				'addition_claim_information',
				'remarks',
				'diagnoses',
				'bills',
				'discharge_code',
				'condition_codes',
				'occurrences',
				'values',
				'insurances',
				'original_claim_id',
				'is_ready_professional_claim',
				'is_ready_institutional_claim',
			],

			'fieldMethods' => [
				'id' => 'int',
				'case_id' => 'int',
				'first_surgeon' => 'firstSurgeon',
				'bill_type' => 'billType',
				'authorization_release_information_payment' => 'authorizationReleaseInfo',
				'has_lab_services_outside' => 'bool',
				'insurance_order' => 'insuranceOrder',
				'diagnoses' => 'diagnoses',
				'bills' => 'bills',
				'discharge_code' => 'dischargeCode',
				'condition_codes' => 'conditionCodes',
				'occurrences' => 'occurrences',
				'values' => 'values',
				'insurances' => 'insurances',
				'amount_paid' => 'amountPaid',
				'is_ready_professional_claim' => 'bool',
				'is_ready_institutional_claim' => 'bool',
				'original_claim_id' => 'int',
			]
		]);
	}

	protected function formatFirstSurgeon($name, $options, $model)
	{
		$case = $model->getCase();
		if ($case) {
			$surgeon = $case->getFirstSurgeon();
			if ($surgeon) {
				return $surgeon->getFullName();
			}
		}
		return '';
	}

	protected function formatDiagnoses($name, $options, $model)
	{
		$result = [];
		foreach ($model->getDiagnoses() as $diagnosis) {
			$result[] = $diagnosis->getBaseFormatter()->toArray();
		}
		return $result;
	}

	protected function formatBills($name, $options, $model)
	{
		$result = [];
		foreach ($model->getBills() as $bill) {
			$result[] = $bill->toArray();
		}
		return $result;
	}

	protected function formatDischargeCode($name, $options, $model)
	{
		if ($model->loaded()) {
			return $model->discharge_code->toArray();
		}
		else {
			/** @var DischargeStatusCode $code */
			$code = clone $model->discharge_code;
			return $code->getDefaultValue()->toArray();
		}
	}

	protected function formatConditionCodes($name, $options, $model)
	{
		$result = [];
		if ($model->loaded()) {
			foreach ($model->condition_codes->find_all()->as_array() as $code) {
				$result[] = $code->toArray();
			}
		}

		return $result;
	}

	protected function formatOccurrences($name, $options, $model)
	{
		$result = [];
		if ($model->loaded()) {
			foreach ($model->occurrences->find_all()->as_array() as $occurrence) {
				$result[] = $occurrence->toArray();
			}
		}

		return $result;
	}

	protected function formatValues($name, $options, $model)
	{
		$result = [];
		if ($model->loaded()) {
			foreach ($model->values->find_all()->as_array() as $value) {
				$result[] = $value->toArray();
			}
		}

		return $result;
	}

	protected function formatInsurances($name, $options, $model)
	{
		$result = [];
		foreach ($model->getInsurances() as $insurance) {
			$result[] = $insurance->toArray();
		}

		return $result;
	}

	protected function formatAmountPaid($name, $options, $model)
	{
		if (empty($model->amount_paid)) {
			return '0.00';
		}
		return $model->amount_paid;
	}

	protected function formatInsuranceOrder($name, $options, $model)
	{
		if (empty($model->insurance_order)) {
			return 1;
		}
		return $model->insurance_order;
	}

	protected function formatAuthorizationReleaseInfo($name, $options, $model)
	{
		if (!$model->loaded()) {
			return true;
		}

		return (bool) $model->authorization_release_information_payment;
	}

	protected function formatBillType($name, $options, $model)
	{
		if (!$model->loaded()) {
			return (string) Coding::BILL_TYPE_ADMIT_THROUGH_DISCHARGE_CODE;
		}

		return $model->bill_type;
	}
}
