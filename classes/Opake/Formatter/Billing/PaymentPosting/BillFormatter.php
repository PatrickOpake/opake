<?php

namespace Opake\Formatter\Billing\PaymentPosting;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\PaymentPosting\EnteredPayment\InsurancePayment;
use Opake\Model\Billing\PaymentPosting\EnteredPayment\PatientPayment;

class BillFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'dos',
			    'provider',
			    'code',
			    'description',
			    'modifiers',
			    'billed_amount',
			    'balance'
			],
			'fieldMethods' => [
				'id' => 'int',
				'dos' => 'dos',
			    'provider' => 'provider',
			    'code' => 'serviceCode',
			    'description' => 'shortDesc',
			    'modifiers' => 'modifiers',
			    'billed_amount' => 'billedAmount',
			    'balance' => 'balance'
			]
		]);
	}

	protected function formatBilledAmount($name, $options, $model)
	{
		return $model->amount;
	}

	protected function formatBalance($name, $options, $model)
	{
		$appliedPayments = $model->applied_payments->find_all();
		$appliedAmount = 0;

		foreach ($appliedPayments as $paymentModel) {
			if ($paymentModel->amount_posted) {
				$appliedAmount += (float) $paymentModel->amount_posted;
			}
		}

		return ((float) $model->amount - $appliedAmount);
	}

	protected function formatModifiers($name, $options, $model)
	{

		if ($modifiers = $model->getModifiersArray()) {
			return implode(', ', $modifiers);
		}

		return 'N/A';
	}

	protected function formatShortDesc($name, $options, $model)
	{
		$chargeMasterRecord = $model->getChargeMasterEntry();
		if ($chargeMasterRecord) {
			return $chargeMasterRecord->desc;
		}

		return '';
	}

	protected function formatServiceCode($name, $options, $model)
	{
		$chargeMasterRecord = $model->getChargeMasterEntry();
		if ($chargeMasterRecord) {
			return $chargeMasterRecord->cpt;
		}

		return '';
	}

	protected function formatProvider($name, $options, $model)
	{
		$case = $model->coding->case;

		if ($case) {
			$surgeon = $case->getFirstSurgeon();
			if ($surgeon) {
				return $surgeon->getFullName();
			}
		}

		return '';
	}

	protected function formatDos($name, $options, $model)
	{
		$value = $model->coding->case->time_start;
		$date = TimeFormat::fromDBDatetime($value);
		if ($date) {
			return TimeFormat::getDate($date);
		}

		return null;
	}

	protected function _formatFloatToMoney($float)
	{
		return '$' . number_format((float) $float, 2, '.', ',');
	}
}