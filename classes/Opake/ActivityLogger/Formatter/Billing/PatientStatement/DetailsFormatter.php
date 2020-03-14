<?php

namespace Opake\ActivityLogger\Formatter\Billing\PatientStatement;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{

		return $value;
	}


	protected function getLabels()
	{
		return [
			'patient' => 'Patient',
		];
	}
}