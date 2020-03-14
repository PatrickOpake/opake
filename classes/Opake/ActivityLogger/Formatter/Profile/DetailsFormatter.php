<?php

namespace Opake\ActivityLogger\Formatter\Profile;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'user':
				return LinkFormatterHelper::formatUserLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'user' => 'User',
		];
	}
}

