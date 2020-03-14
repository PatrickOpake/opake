<?php

namespace Opake\ActivityLogger\Formatter\Billing\CaseBilling\Items;

use Opake\ActivityLogger\Formatter\ArrayRowFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\Helper\TimeFormat;

class SupplyRowFormatter extends ArrayRowFormatter
{
	protected function formatLabel($id)
	{
		return 'Supply #' . $id;
	}

	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'hcpcs_id':
				return FormatterHelper::formatInventoryHCPCSName($this->pixie, $value);

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
			'type_id' => 'Type',
			'qty' => 'Quantity',
			'hcpcs_id' => 'HCPCS',
			'cost' => 'Cost',
			'date' => 'Date',
			'modifier1_id' => 'Mod 1',
			'modifier2_id' => 'Mod 2',
		];
	}
}