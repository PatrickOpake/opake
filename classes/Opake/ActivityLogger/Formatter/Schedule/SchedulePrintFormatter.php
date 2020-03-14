<?php

namespace Opake\ActivityLogger\Formatter\Schedule;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class SchedulePrintFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'location':
				return FormatterHelper::formatLocation($this->pixie, $value);

			case 'doctor':
				return FormatterHelper::formatUser($this->pixie, $value);

			case 'procedure':
				return FormatterHelper::formatProcedure($this->pixie, $value);

			case 'patient':
				return FormatterHelper::formatPatientName($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'title' => 'Calendar',
			'id' => 'Account Number',
			'location' => 'Room',
			'doctor' => 'Surgeon',
			'procedure' => 'Procedure',
			'patient' => 'Patient'
		];
	}
}
