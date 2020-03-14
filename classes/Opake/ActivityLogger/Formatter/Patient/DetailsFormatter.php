<?php

namespace Opake\ActivityLogger\Formatter\Patient;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'patient':
				return LinkFormatterHelper::formatPatientLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'patient' => 'Patient',
		];
	}
}

