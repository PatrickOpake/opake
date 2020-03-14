<?php

namespace Opake\ActivityLogger\Formatter\Clinical\Items;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'user':
				return FormatterHelper::formatUser($this->pixie, $value);
			case 'inventory_item':
				return FormatterHelper::formatInventoryItemName($this->pixie, $value);
		}

		return $value;
	}

	protected function getIgnored()
	{
		return [
			'card'
		];
	}

	protected function getLabels()
	{
		return [
			'user' => 'Surgeon',
			'type' => 'Stage',
			'text' => 'Text',
			'inventory_item' => 'Inventory Item'
		];
	}
}