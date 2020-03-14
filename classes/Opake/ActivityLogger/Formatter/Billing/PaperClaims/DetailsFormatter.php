<?php

namespace Opake\ActivityLogger\Formatter\Billing\PaperClaims;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'case_ids':
				return LinkFormatterHelper::formatCasesLinkList($this->pixie, $value);

			case 'patients':
				return implode(', ', $value);

		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'case_ids' => 'Case',
			'patients' => 'Patient',
		];
	}
}