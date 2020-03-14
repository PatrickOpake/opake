<?php

namespace Opake\ActivityLogger\Formatter\Booking\File;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'booking_id':
				return LinkFormatterHelper::formatBookingLink($this->pixie, $value);
			case 'chart_id':
				return LinkFormatterHelper::formatCaseChartFileLink($this->pixie, $value);
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'booking_id' => 'Booking',
		    'chart_id' => 'File'
		];
	}
}