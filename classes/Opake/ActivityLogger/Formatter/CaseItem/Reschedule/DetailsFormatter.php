<?php

namespace Opake\ActivityLogger\Formatter\CaseItem\Reschedule;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'old_dos':
				return FormatterHelper::formatDateAndTime($value);
			case 'new_dos':
				return FormatterHelper::formatDateAndTime($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
		    'old_dos' => 'Original DOS',
		    'new_dos' => 'Rescheduled DOS'
		];
	}
}