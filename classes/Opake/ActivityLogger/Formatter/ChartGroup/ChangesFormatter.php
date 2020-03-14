<?php

namespace Opake\ActivityLogger\Formatter\ChartGroup;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'charts':
				return FormatterHelper::formatChartsList($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'name' => 'Name',
			'charts' => 'Charts',
		];
	}
}

