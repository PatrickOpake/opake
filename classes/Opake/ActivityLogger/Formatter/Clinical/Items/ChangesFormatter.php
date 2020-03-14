<?php

namespace Opake\ActivityLogger\Formatter\Clinical\Items;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'status':
				return $this->formatCheckedStatus($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'status' => 'Status',
			'text' => 'Text',
			'quantity' => 'Quantity',
		];
	}

	private function formatCheckedStatus($value)
	{
		return ($value) ? 'Checked' : 'Unchecked';
	}
}