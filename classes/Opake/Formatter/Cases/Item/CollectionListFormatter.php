<?php

namespace Opake\Formatter\Cases\Item;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Cases\Coding\Bill;
use Opake\Model\Insurance\AbstractType;
use OpakeAdmin\Helper\Billing\Ledger\CasePatientResponsibilityCalculator;

class CollectionListFormatter extends BaseDataFormatter
{

	protected $charges = 0;
	protected $payments = 0;
	protected $balance = 0;
	protected $adjustment = 0;
	protected $writeoff = 0;

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'case_id',
				'time_start',
				'patient',
				'primary_payer_type',
				'primary_payer_name',
				'primary_payer_phone',
				'primary_payer_policy_id',
				'secondary_payer_type',
				'secondary_payer_name',
				'secondary_payer_phone',
				'secondary_payer_policy_id',
				'surgeon',
				'procedures',
				'notes_count',
				'has_billing_flagged_comments',
				'charges',
				'payments',
				'adjustment',
				'balance',
				'writeoff',
				'billing_status',
				'responsibility',
				'sum_adjustment_writeoff'
			],
			'fieldMethods' => [
				'case_id' => 'caseId',
				'time_start' => 'timeStart',
				'patient' => 'patient',
				'primary_payer_type' => 'deferred',
				'primary_payer_name' => 'deferred',
				'primary_payer_phone' => 'deferred',
				'primary_payer_policy_id' => 'deferred',
				'secondary_payer_type' => 'deferred',
				'secondary_payer_name' => 'deferred',
				'secondary_payer_phone' => 'deferred',
				'secondary_payer_policy_id' => 'deferred',
				'surgeon' => 'surgeon',
				'procedures' => 'procedures',
				'notes_count' => 'notesCount',
				'has_billing_flagged_comments' => 'hasFlaggedComments',
				'charges' => 'charges',
				'payments' => 'payments',
				'adjustment' => 'adjustment',
				'balance' => 'balance',
				'writeoff' => 'writeoff',
				'responsibility' => 'responsibility',
				'sum_adjustment_writeoff' => 'sumAdjustmentWriteOff'
			]
		]);
	}

	/**
	 * @param $data
	 * @param $fields
	 * @return mixed
	 */
	protected function prepareDeferredData($data, $fields)
	{
		$primaryInsurance = $this->model->registration->getPrimaryInsurance();
		$secondaryInsurance = $this->model->registration->getSecondaryInsurance();
		$insuranceTypes = AbstractType::getInsuranceTypesList();

		if (in_array('primary_payer_type', $fields)) {
			if($primaryInsurance) {
				$data['primary_payer_type'] = (isset($insuranceTypes[$primaryInsurance->type])) ? $insuranceTypes[$primaryInsurance->type] : '';
			}
		}

		if (!!array_intersect(['primary_payer_name', 'primary_payer_phone', 'primary_payer_policy_id'], $fields)) {
			if ($primaryInsurance && $primaryInsurance->isRegularInsurance()) {
				$primaryInsuranceDataModel = $primaryInsurance->getInsuranceDataModel();
				$data['primary_payer_name'] =  $primaryInsuranceDataModel->insurance->name;
				if(empty($data['primary_payer_name'])) {
					$data['primary_payer_name'] = $data['primary_payer_type'];
				}
				$data['primary_payer_phone'] = $primaryInsuranceDataModel->phone;
				$data['primary_payer_policy_id'] = $primaryInsuranceDataModel->policy_number;
			} elseif($primaryInsurance && ($primaryInsurance->isAutoAccidentInsurance() || $primaryInsurance->isWorkersCompanyInsurance())) {
				$primaryInsuranceDataModel = $primaryInsurance->getInsuranceDataModel();
				$data['primary_payer_name'] =  $primaryInsuranceDataModel->insurance_company->name;
				$data['primary_payer_phone'] = $primaryInsuranceDataModel->insurance_company_phone;
//				$data['primary_payer_policy_id'] = $primaryInsuranceDataModel->policy_number;
			} else {
				$data['primary_payer_name'] =  $data['primary_payer_type'];
			}
		}

		if (in_array('secondary_payer_type', $fields)) {
			if($secondaryInsurance) {
				$data['secondary_payer_type'] = (isset($insuranceTypes[$secondaryInsurance->type])) ? $insuranceTypes[$secondaryInsurance->type] : '';
			}
		}

		if (!!array_intersect(['secondary_payer_name', 'secondary_payer_phone', 'secondary_payer_policy_id'], $fields)) {
			if ($secondaryInsurance && $secondaryInsurance->isRegularInsurance()) {
				$secondaryInsuranceDataModel = $secondaryInsurance->getInsuranceDataModel();
				$data['secondary_payer_name'] =  $secondaryInsuranceDataModel->insurance->name;
				if(empty($data['secondary_payer_name'])) {
					$data['secondary_payer_name'] = $data['secondary_payer_type'];
				}
				$data['secondary_payer_phone'] = $secondaryInsuranceDataModel->phone;
				$data['secondary_payer_policy_id'] = $secondaryInsuranceDataModel->policy_number;
			} elseif($secondaryInsurance && ($secondaryInsurance->isAutoAccidentInsurance() || $secondaryInsurance->isWorkersCompanyInsurance())) {
				$secondaryInsuranceDataModel = $secondaryInsurance->getInsuranceDataModel();
				$data['secondary_payer_name'] =  $secondaryInsuranceDataModel->insurance_company->name;
				$data['secondary_payer_phone'] = $secondaryInsuranceDataModel->insurance_company_phone;
//				$data['secondary_payer_policy_id'] = $secondaryInsuranceDataModel->policy_number;
			} else {
				$data['secondary_payer_name'] =  $data['secondary_payer_type'];
			}
		}

		return $data;
	}

	protected function formatResponsibility($name, $options, $model)
	{
		$calc = new CasePatientResponsibilityCalculator($model);
		return $calc->calculateResponsibilityDetails();
	}

	protected function formatCaseId($name, $options, $model)
	{
		return $model->id();
	}

	protected function formatTimeStart($name, $options, $model)
	{
		$value = $model->time_start;
		$date = TimeFormat::fromDBDatetime($value);
		if ($date) {
			return TimeFormat::getDate($value);
		}

		return null;
	}

	protected function formatPatient($name, $options, $model)
	{
		$patient = $model->registration->patient;
		return [
			'first_name' => $patient->first_name,
			'last_name' => $patient->last_name,
			'full_mrn' => $patient->getFullMrn(),
		];
	}

	protected function formatSurgeon($name, $options, $model)
	{
		if($firstSurgeon = $model->getFirstSurgeon()) {
			return $firstSurgeon->getFullName();
		}
	}

	protected function formatProcedures($name, $options, $model)
	{
		$bills = [];
		foreach ($model->ledger_interest_payments->order_by('id', 'desc')->find_all() as $ledgerInterestPayment) {
			$amount = $this->_formatFloatToMoney($ledgerInterestPayment->amount);
			$bills[] = [
				'id' => $ledgerInterestPayment->id(),
				'code' => 'INT',
				'charge' => $amount,
				'payment' => $amount,
			];
			$this->charges += $ledgerInterestPayment->amount;
			$this->payments += $ledgerInterestPayment->amount;
		}

		/** @var Bill $bill */
		foreach ($model->coding->getBills() as $bill)
		{
			$this->charges += $bill->charge;
			$this->payments += $bill->getPayment();
			$this->balance += $bill->getRemainder();
			$this->adjustment += $bill->getAdjustment();
			$this->writeoff += $bill->getWriteOff();
			$bills[] = $bill->getFormatter('Collection')->toArray();
		}

		return $bills;
	}

	protected function formatNotesCount($name, $options, $model)
	{
		return (int)$model->getBillingNotesCount();
	}

	protected function formatHasFlaggedComments($name, $options, $model)
	{
		return $model->hasFlaggedBillingComments();
	}

	protected function formatCharges($name, $options, $model)
	{

		return $this->_formatFloatToMoney($this->charges);
	}

	protected function formatPayments($name, $options, $model)
	{
		return $this->_formatFloatToMoney($this->payments);
	}

	protected function formatBalance($name, $options, $model)
	{
		return $this->_formatFloatToMoney($this->balance);
	}

	protected function formatAdjustment($name, $options, $model)
	{
		return $this->_formatFloatToMoney($this->adjustment);
	}

	protected function formatWriteoff($name, $options, $model)
	{
		return $this->_formatFloatToMoney($this->writeoff);
	}

	protected function formatSumAdjustmentWriteOff($name, $options, $model)
	{
		return $this->_formatFloatToMoney($this->adjustment + $this->writeoff);
	}

	protected function _formatFloatToMoney($float)
	{
		return '$' . number_format((float) $float, 2, '.', ',');
	}


}
