<?php

namespace Opake\ActivityLogger\Formatter\Settings\OperativeReportTemplates;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{

	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'template':
				return LinkFormatterHelper::formatOpReportTemplateLink($this->pixie, $value);

		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'template' => 'Template',
		];
	}
}