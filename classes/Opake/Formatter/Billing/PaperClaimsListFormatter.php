<?php

namespace Opake\Formatter\Billing;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;

class PaperClaimsListFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
			    'id',
			    'claim_id',
			    'patient_name',
			    'dos',
			    'dob',
			    'insurance_payer_name',
			    'last_transaction_date',
			    'type',
			    'notes_count',
			    'has_billing_flagged_comments',
			],
			'fieldMethods' => [
			    'id' => ['alias',  ['alias' => 'case_id']],
			    'claim_id' => ['alias',  ['alias' => 'id']],
			    'patient_name' => 'patientName',
			    'dos' => 'dos',
			    'dob' => 'patientDob',
			    'insurance_payer_name' => 'insurancePayer',
			    'last_transaction_date' => 'transactionDate',
			    'type' => 'type',
			    'notes_count' => 'notesCount',
			    'has_billing_flagged_comments' => 'hasFlaggedComments'
			]
		]);
	}

	protected function formatPatientName($name, $options, $model)
	{
		$reg = $model->case->registration;
		return $reg->getFullName();
	}

	protected function formatPatientDob($name, $options, $model)
	{
		$reg = $model->case->registration;
		if($reg->dob) {
			return TimeFormat::formatToJsDate($reg->dob);
		}

		return null;
	}

	protected function formatDos($name, $options, $model)
	{
		$case = $model->case;
		if ($case->time_start) {
			return TimeFormat::formatToJsDate($case->time_start);
		}

		return null;
	}

	protected function formatInsurancePayer($name, $options, $model)
	{
		if ($model->primary_insurance->loaded()) {
			return $model->primary_insurance->getInsuranceName();
		}

		$insurancePayerId = $model->insurance_payer_id;
		if ($insurancePayerId) {
			$payerModel = $this->pixie->orm->get('Insurance_Payor', $insurancePayerId);
			if ($payerModel->loaded()) {
				return $payerModel->name;
			}
		}

		return '';
	}

	protected function formatTransactionDate($name, $options, $model)
	{
		$value = $model->last_transaction_date;
		$date = TimeFormat::fromDBDatetime($value);
		if ($date) {
			return TimeFormat::getDate($value);
		}

		return null;
	}

	protected function formatType($name, $options, $model)
	{
		if($model->type == Claim::TYPE_1500) {
			return '1500';
		} elseif($model->type == Claim::TYPE_UB04) {
			return 'UB04';
		}

		return '';
	}

	protected function formatNotesCount($name, $options, $model)
	{
		return (int)$model->case->getBillingNotesCount();
	}

	protected function formatHasFlaggedComments($name, $options, $model)
	{
		return $model->case->hasFlaggedBillingComments();
	}
}