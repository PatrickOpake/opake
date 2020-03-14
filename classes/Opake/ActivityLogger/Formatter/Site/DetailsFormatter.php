<?php

namespace Opake\ActivityLogger\Formatter\Site;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'site':
				return LinkFormatterHelper::formatSiteLink($this->pixie, $value);
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'site' => 'Site'
		];
	}
}

