<?php

namespace Opake\ActivityLogger\Formatter\Settings\Forms;

use Opake\ActivityLogger\DefaultFormatter;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'segment':
				return $this->formatSegment($value);

		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'segment' => 'Segment',
			'name' => 'Name',
		];
	}

	protected function formatSegment($value)
	{
		if ($value === 'billing') {
			return 'Billing';
		}

		if ($value === 'intake') {
			return 'Intake';
		}

		return $value;
	}
}