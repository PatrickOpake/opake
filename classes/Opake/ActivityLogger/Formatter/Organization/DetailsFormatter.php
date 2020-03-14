<?php

namespace Opake\ActivityLogger\Formatter\Organization;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'organization':
				return LinkFormatterHelper::formatOrganizationLink($value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'organization' => 'Organization',
		];
	}
}
