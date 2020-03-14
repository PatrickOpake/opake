<?php

namespace Opake\Formatter\Billing\Ledger;

use Opake\Formatter\BaseDataFormatter;
use Opake\Formatter\Billing\Ledger\PaymentActivity\FormFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Ledger\PaymentActivity;

class PaymentActivityFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'payment_type_code',
				'payment_type_description',
				'activity_date',
				'check_number',
				'claim_id',
			    'amount',
			    'notes',
			    'form',
			],
			'fieldMethods' => [
				'id' => 'int',
			    'payment_type_code' => 'paymentTypeCode',
			    'payment_type_description' => 'paymentTypeDescription',
			    'activity_date' => 'toDate',
			    'amount' => 'amount',
			    'notes' => 'notes',
			    'form' => 'form'
			]
		]);
	}

	protected function formatPaymentTypeCode($name, $options, $model)
	{
		$options = PaymentActivity::getPaymentTypeCodeOptions();
		return ($options[$model->payment_type]) ? $options[$model->payment_type] : '';
	}

	protected function formatPaymentTypeDescription($name, $options, $model)
	{
		$options = PaymentActivity::getPaymentTypeDescriptionOptions();
		if ($model->payment_type == PaymentActivity::PAYMENT_TYPE_CUSTOM) {
			return $model->custom_payment_type;
		}
		return ($options[$model->payment_type]) ? $options[$model->payment_type] : '';
	}

	protected function formatNotes($name, $options, $model)
	{
		$notes = [];
		if ($model->applied_payment->loaded()) {
			foreach ($model->applied_payment->notes->find_all() as $note) {
				$notes[] = [
					'id' => (int) $note->id(),
					'user' => [
						'id' => (int) $note->user->id(),
						'first_name' => $note->user->first_name,
						'last_name' => $note->user->last_name
					],
					'text' => $note->text,
					'time_added' => TimeFormat::formatToJsDate($note->time_added)
				];
			}
		}

		return $notes;
	}

	protected function formatAmount($name, $options, $model)
	{
		return $model->amount;
	}

	protected function formatForm($name, $options, $model)
	{
		$formatter = new FormFormatter($model);
		return $formatter->toArray();
	}
}