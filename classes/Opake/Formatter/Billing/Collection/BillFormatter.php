<?php

namespace Opake\Formatter\Billing\Collection;

use Opake\Formatter\BaseDataFormatter;
use OpakeAdmin\Helper\Billing\Ledger\PatientResponsibilityCalculator;

class BillFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'code',
			    'payment',
			    'balance',
				'charge',
				'amount',
				'writeoff',
				'writeoff_adj',
				'responsibility',
				'fee'

			],
			'fieldMethods' => [
				'id' => 'int',
				'code' => 'serviceCode',
			    'payment' => 'payment',
			    'balance' => 'remainder',
				'charge' => 'charge',
			    'amount' => 'adjustments',
			    'writeoff' => 'writeOff',
				'writeoff_adj' => 'writeOffAndAdjustments',
				'responsibility' => 'responsibility',
				'fee' => 'fee'

			]
		]);
	}

	protected function formatServiceCode($name, $options, $model)
	{
		$chargeMasterRecord = $model->getChargeMasterEntry();
		if ($chargeMasterRecord) {
			return $chargeMasterRecord->cpt;
		}

		return '';
	}

	protected function formatPayment($name, $options, $model)
	{
		$sumAmount = $model->getPayment();
		return $this->_formatFloatToMoney($sumAmount);
	}

	protected function formatRemainder($name, $options, $model)
	{
		$amount = $model->getRemainder();
		return $this->_formatFloatToMoney($amount);
	}

	protected function formatCharge($name, $options, $model)
	{
		$charge = $model->charge;
		return $this->_formatFloatToMoney($charge);
	}

	protected function formatAdjustments($name, $options, $model)
	{
		$adjustment = $model->getAdjustment();
		return $this->_formatFloatToMoney($adjustment);
	}

	protected function formatWriteOff($name, $options, $model)
	{
		$writeoff = $model->getWriteOff();
		return $this->_formatFloatToMoney($writeoff);
	}

	protected function formatResponsibility($name, $options, $model)
	{
		$case = $model->coding->case;
		$casePatient = $case->registration->patient;
		$calculator = new PatientResponsibilityCalculator($casePatient, $case, $model);
		return $calculator->calculateResponsibilityDetails();
	}

	protected function formatFee($name, $options, $model)
	{
		$fee = $model->fee;
		if(empty($fee)) {
			return [];
		}
		return $fee->toArray();
	}

	protected function formatWriteOffAndAdjustments($name, $options, $model)
	{
		$adjustment = $model->getAdjustment();
		$writeoff = $model->getWriteOff();
		return $this->_formatFloatToMoney($adjustment + $writeoff);
	}

	protected function _formatFloatToMoney($float)
	{
		return '$' . number_format((float) $float, 2, '.', ',');
	}
}