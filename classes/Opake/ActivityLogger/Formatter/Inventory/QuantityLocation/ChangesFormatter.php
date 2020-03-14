<?php

namespace Opake\ActivityLogger\Formatter\Inventory\QuantityLocation;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'location_id':
				return FormatterHelper::formatLocation($this->pixie, $value);
			case 'distributor_id':
				return FormatterHelper::formatVendor($this->pixie, $value);
			case 'exp_date':
				return FormatterHelper::formatDate($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'location_id' => 'Location_Storage',
			'distributor_id' => 'Distributor',
			'exp_date' => 'Expiration Date',
			'quantity' => 'Quantity',
		];
	}
}