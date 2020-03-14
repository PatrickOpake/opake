<?php

namespace Opake\Formatter\Billing\Navicure\Payment\Service;

use Opake\Formatter\Billing\Navicure\Payment\BasePaymentFormatter;

class ListEntryFormatter extends BasePaymentFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'hcpcs',
			    'quantity',
			    'charge_amount',
			    'allowed_amount',
			    'payment',
			    'deduct_adjustments',
			    'co_pay_co_ins_adjustments',
			    'other_adjustments',
			    'balance',
			    'provider_status_code',
			    'adjustments'
			],
			'fieldMethods' => [
				'id' => 'int',
			    'quantity' => 'quantity',
				'charge_amount' => 'money',
			    'allowed_amount' => 'money',
			    'payment' => 'money',
			    'deduct_adjustments' => 'money',
			    'other_adjustments' => 'money',
			    'co_pay_co_ins_adjustments' => 'coPayCoInsAdjustments',
			    'balance' => 'balance',
			    'adjustments' => 'adjustments'
			]
		]);
	}

	protected function formatQuantity($name, $options, $model)
	{
		if (!empty($model->quantity)) {
			return $model->quantity;
		}

		return '';
	}

	protected function formatBalance($name, $options, $model)
	{
		$count = $model->allowed_amount;
		if ($model->payment) {
			$count -= $model->payment;
		}

		return $this->_formatFloatToMoney($count);
	}

	protected function formatCoPayCoInsAdjustments($name, $options, $model)
	{
		return $this->_formatFloatToMoney($model->getCoInsCoPayAdjustmentsSum());
	}

	protected function formatAdjustments($name, $options, $model)
	{
		$data = [];
		foreach ($model->adjustments->find_all() as $adjustment) {
			$data[] = $adjustment->getFormatter('ListEntry')->toArray();
		}

		return $data;
	}
}