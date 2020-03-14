<?php

namespace Opake\ActivityLogger\Formatter\Clinical\Note;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'case_id':
				return LinkFormatterHelper::formatCaseLink($this->pixie, $value);

		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'case_id' => 'Case',
			'patient' => 'Patient',
			'text' => 'Text'
		];
	}
}