<?php

namespace Opake\ActivityLogger\Formatter\CaseItem\CheckIn;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'date':
				return FormatterHelper::formatDateAndTime($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'date' => 'Check In Date',
		];
	}
}