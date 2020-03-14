<?php

namespace Opake\ActivityLogger\Formatter\Settings\SmsTemplate;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'org':
				return LinkFormatterHelper::formatOrganizationLink($value);

		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'org' => 'Organization',
		];
	}
}