<?php

namespace Opake\ActivityLogger\Formatter\Billing\CaseBilling;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'case':
				return LinkFormatterHelper::formatCaseLink($this->pixie, $value);
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'case' => 'Case',
		];
	}
}