<?php

namespace Opake\ActivityLogger\Formatter\Booking\Printing;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'booking_ids':
				return LinkFormatterHelper::formatBookingsLinkList($this->pixie, $value);
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'booking_ids' => 'Booking'
		];
	}
}