<?php

namespace Opake\ActivityLogger\Formatter\Billing\Payments;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;
use Opake\Helper\TimeFormat;

class DeleteFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'case_id':
				return LinkFormatterHelper::formatCaseLink($this->pixie, $value);
			case 'total_amount':
			case 'payment_amount':
				 return '$' . number_format((float)$value, 2, '.', ',');
			case 'payment_date':
				$date = TimeFormat::fromDBDate($value);
				return TimeFormat::getDate($date);
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'case_id' => 'Case',
			'payment_date' => 'Payment date',
			'payment_source' => 'Payment Source',
			'payment_method' => 'Payment Method',
			'payment_amount' => 'Amount'
		];
	}
}