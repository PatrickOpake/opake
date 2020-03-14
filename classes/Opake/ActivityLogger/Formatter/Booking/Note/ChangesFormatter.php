<?php

namespace Opake\ActivityLogger\Formatter\Booking\Note;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\Helper\StringHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{

		switch ($key) {
			case 'text':
				return StringHelper::truncate($value, 200);
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'text' => 'Text'
		];
	}
}