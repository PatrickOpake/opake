<?php

namespace Opake\ActivityLogger\Formatter\CaseBlock\Block;

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
			case 'date_from':
			case 'date_to':
				return FormatterHelper::formatDate($value);
			case 'time_from':
			case 'time_to':
				return FormatterHelper::formatTime($value);
			case 'week_number':
				return FormatterHelper::formatRecurrenceWeekNumber($value);
			case 'recurrence_monthly_day':
				return FormatterHelper::formatRecurrenceMonthlyDay($value);
			case 'recurrence_week_days':
				return FormatterHelper::formatRecurrenceWeekDays($value);
			case 'recurrence_every':
				return FormatterHelper::formatRecurrenceFrequency($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'location_id' => 'Room',
			'doctor_id' => 'Doctor',
			'color' => 'Color',
			'date_from' => 'Date Range From',
			'date_to' => 'Date Range To',
			'time_from' => 'Time Range From',
			'time_to' => 'Time Range To',
			'recurrence_every' => 'Frequency',
			'recurrence_week_days' => 'Day Of Week',
			'recurrence_monthly_day' => 'Day Of Month',
			'week_number' => 'Every Week',
		];
	}
}

