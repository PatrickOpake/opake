<?php

namespace Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items;

use Opake\ActivityLogger\Formatter\ArrayRowFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\Helper\TimeFormat;

class ProcedureRowFormatter extends ArrayRowFormatter
{
	protected function formatLabel($id)
	{
		return 'Procedure #' . $id;
	}

	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'cpt_id':
				return FormatterHelper::formatProcedure($this->pixie, $value);

			case 'modifier1_id':
			case 'modifier2_id':
				return FormatterHelper::formatModifier($this->pixie, $value);

			case 'date':
				$dt = TimeFormat::fromDBDatetime($value);
				return (string) TimeFormat::getDate($dt);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'qty' => 'Quantity',
			'cpt_id' => 'Code',
			'cost' => 'Cost',
			'date' => 'Date',
			'modifier1_id' => 'Mod 1',
			'modifier2_id' => 'Mod 2'
		];
	}
}