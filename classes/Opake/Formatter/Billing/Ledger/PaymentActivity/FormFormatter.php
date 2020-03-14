<?php

namespace Opake\Formatter\Billing\Ledger\PaymentActivity;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;

class FormFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'activity_date',
			    'payment_type',
			    'amount',
			    'check_number',
			    'custom_payment_type',
			    'claim_id',
			    'notes'
			],
			'fieldMethods' => [
				'id' => 'int',
				'amount' => ['float', [
					'round' => 2,
					'nullAsZero' => true
				]],
			    'activity_date' => 'toJsDate',
			    'payment_type' => 'int',
			    'notes' => 'notes'
			]
		]);
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

}