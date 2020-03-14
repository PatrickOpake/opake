<?php

namespace Opake\ActivityLogger\Formatter\Inventory\Order;


use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class OutgoingDetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'order':
				return LinkFormatterHelper::formatOutgoingOrderLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'order' => 'Order'
		];
	}
}