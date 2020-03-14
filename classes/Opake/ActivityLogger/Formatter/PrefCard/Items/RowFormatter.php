<?php

namespace Opake\ActivityLogger\Formatter\PrefCard\Items;

use Opake\ActivityLogger\Formatter\ArrayRowFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class RowFormatter extends ArrayRowFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'inventory_id':
				return FormatterHelper::formatInventoryItemName($this->pixie, $value);
		}
		return $value;
	}

	protected function getLabels()
	{
		return [
			'text' => 'Text',
			'inventory_id' => 'Item',
			'quantity' => 'Quantity'
		];
	}
}