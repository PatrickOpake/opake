<?php

namespace Opake\ActivityLogger\Formatter\Schedule;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class CalendarSettingsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'colors':
				return FormatterHelper::formatCaseColors($this->pixie, $value);

			case 'block_overwrite':
				return FormatterHelper::formatOnOff($value);

			case 'block_timing':
				return FormatterHelper::formatCaseBlockTiming($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'block_timing' => 'Block Timing',
			'block_overwrite' => 'Block Overwrite',
			'colors' => 'Case Colors'
		];
	}
}
