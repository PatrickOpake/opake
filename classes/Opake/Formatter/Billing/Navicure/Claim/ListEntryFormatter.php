<?php

namespace Opake\Formatter\Billing\Navicure\Claim;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;

class ListEntryFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'case_id',
			    'patient_name',
			    'mrn',
			    'dos',
			    'insurance_payer_name',
			    'transaction_date',
			    'status',
			    'type_title'
			],
			'fieldMethods' => [
				'id' => 'int',
			    'case_id' => 'int',
			    'patient_name' => 'patientName',
			    'dos' => 'dos',
			    'insurance_payer_name' => 'insurancePayer',
			    'transaction_date' => 'transactionDate',
			    'status' => 'status',
			    'type_title' => 'typeTitle'
			]
		]);
	}

	protected function formatPatientName($name, $options, $model)
	{
		return $model->last_name . ', ' . $model->first_name;
	}

	protected function formatDos($name, $options, $model)
	{
		$value = $model->dos;
		$date = TimeFormat::fromDBDatetime($value);
		if ($date) {
			return TimeFormat::getDate($value);
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

	protected function formatStatus($name, $options, $model)
	{
		$statuses = Claim::getListOfStatusDescription();
		if (isset($statuses[$model->status])) {
			return $statuses[$model->status];
		}

		return null;
	}

	protected function formatTypeTitle($name, $options, $model)
	{
		$types = Claim::getListOfElectronicClaimType();
		if (isset($types[$model->type])) {
			return $types[$model->type];
		}

		return '';
	}
}