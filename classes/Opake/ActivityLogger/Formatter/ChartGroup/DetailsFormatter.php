<?php

namespace Opake\ActivityLogger\Formatter\ChartGroup;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'chart_group':
				return FormatterHelper::formatChartGroupName($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'chart_group' => 'Chart Group',
		];
	}
}

