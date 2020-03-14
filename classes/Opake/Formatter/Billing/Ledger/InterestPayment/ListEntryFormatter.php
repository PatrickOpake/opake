<?php

namespace Opake\Formatter\Billing\Ledger\InterestPayment;

use Opake\Formatter\BaseDataFormatter;

class ListEntryFormatter extends BaseDataFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return [
			'fields' => [
				'id',
				'amount',
				'date',
			],
			'fieldMethods' => [
				'id' => 'int',
				'amount' => ['float', [
					'round' => 2,
				    'nullAsZero' => true
				]],
				'date' => 'toDate'
			]
		];
	}
}