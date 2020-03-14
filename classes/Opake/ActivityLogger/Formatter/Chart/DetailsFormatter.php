<?php

namespace Opake\ActivityLogger\Formatter\Chart;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'chart':
				return LinkFormatterHelper::formatChartLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'chart' => 'Chart',
			'name' => 'Chart Name',
		];
	}
}

