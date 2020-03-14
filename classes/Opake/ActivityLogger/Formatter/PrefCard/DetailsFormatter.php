<?php

namespace Opake\ActivityLogger\Formatter\PrefCard;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'id':
				return LinkFormatterHelper::formatPrefCardLink($this->pixie, $value, $this->data['card']);
			case 'user':
				return FormatterHelper::formatUser($this->pixie, $value);
			case 'room':
				return FormatterHelper::formatLocation($this->pixie, $value);
		}
		return $value;
	}

	protected function getIgnored()
	{
		return ['card'];
	}

	protected function getLabels()
	{
		return [
			'id' => 'ID',
			'user' => 'Surgeon',
			'room' => 'Room'
		];
	}
}