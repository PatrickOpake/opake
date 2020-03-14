<?php

namespace Opake\ActivityLogger\Formatter\Clinical\Verification;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'case_id':
				return LinkFormatterHelper::formatCaseLink($this->pixie, $value);

			case 'registration_id':
				return LinkFormatterHelper::formatVerificationLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'case_id' => 'Case',
			'registration_id' => 'Registration',
			'patient' => 'Patient',
			'insurance' => 'Insurance',
		];
	}
}