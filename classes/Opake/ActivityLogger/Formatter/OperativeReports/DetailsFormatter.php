<?php

namespace Opake\ActivityLogger\Formatter\OperativeReports;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'report':
				return LinkFormatterHelper::formatReportLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'report' => 'Operative Report',
		];
	}
}

