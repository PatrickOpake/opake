<?php

namespace Opake\ActivityLogger\Formatter\CaseItem\Delete;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'dos':
				return FormatterHelper::formatDateAndTime($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'dos' => 'DOS'
		];
	}
}