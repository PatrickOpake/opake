<?php

namespace Opake\ActivityLogger\Formatter\Inventory\QuantityLocation;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'item':
				return LinkFormatterHelper::formatInventoryItemLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'item' => 'Item',
		];
	}
}