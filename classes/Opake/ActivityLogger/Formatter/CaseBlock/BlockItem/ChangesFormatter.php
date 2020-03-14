<?php

namespace Opake\ActivityLogger\Formatter\CaseBlock\BlockItem;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'location_id':
				return FormatterHelper::formatLocation($this->pixie, $value);
			case 'doctor_id':
				return FormatterHelper::formatUser($this->pixie, $value);
			case 'color':
				return FormatterHelper::formatColor($value);
			case 'start':
			case 'end':
				return FormatterHelper::formatDateAndTime($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'location_id' => 'Room',
			'doctor_id' => 'Doctor',
			'color' => 'Color',
			'start' => 'Date Range From',
			'end' => 'Date Range To',
		];
	}
}
