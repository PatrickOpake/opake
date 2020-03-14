<?php

namespace Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items;

use Opake\ActivityLogger\Formatter\ArrayRowFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\Helper\TimeFormat;

class OccurrenceRowFormatter extends ArrayRowFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'occ_id':
			case 'cond_id':
				return FormatterHelper::formatCondition($this->pixie, $value);

			case 'occurence_date':
				$dt = TimeFormat::fromDBDatetime($value);
				return (string) TimeFormat::getDate($dt);
		}

		return $value;
	}

	protected function formatLabel($id)
	{
		return 'Occurrence #' . $id;
	}

	protected function getLabels()
	{
		return [
			'cond_id' => 'Condition Code',
			'occ_id' => 'Occurrence Code',
			'occurence_date' => 'Date'
		];
	}
}