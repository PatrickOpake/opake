<?php

namespace Opake\ActivityLogger\Formatter\PrefCard;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'case_type_id':
				return FormatterHelper::formatProcedure($this->pixie, $value);
			case 'items':
			case 'notes':
				return FormatterHelper::formatArrayOfChanges($this->pixie, $value , '\Opake\ActivityLogger\Formatter\PrefCard\Items\RowFormatter');
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'case_type_id' => 'Procedure',
			'items' => 'Inventory Items',
			'notes' => 'Checklist'
		];
	}
}