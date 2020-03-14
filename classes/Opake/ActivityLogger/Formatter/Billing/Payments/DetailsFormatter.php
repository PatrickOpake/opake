<?php

namespace Opake\ActivityLogger\Formatter\Billing\Payments;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'case_id':
				return LinkFormatterHelper::formatCaseLink($this->pixie, $value);
			case 'total_amount':
				 return '$' . number_format((float)$value, 2, '.', ',');

		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'case_id' => 'Case',
			'payment_source' => 'Payment Source',
			'payment_method' => 'Payment Method',
			'total_amount' => 'Amount'
		];
	}
}