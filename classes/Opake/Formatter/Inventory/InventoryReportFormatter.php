<?php

namespace Opake\Formatter\Inventory;

use Opake\Formatter\BaseDataFormatter;

class InventoryReportFormatter extends BaseDataFormatter
{

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'item_number',
				'name',
				'desc',
				'unit_price',
				'default_qty',
				'actual_use',
				'total_cost'
			],

			'fieldMethods' => [
				'id' => 'int',
				'unit_price' => 'UnitPrice',
				'default_qty' => 'int',
				'actual_use' => 'int',
				'total_cost' => 'TotalCost',
			]
		]);
	}

	protected function formatUnitPrice($name, $options, $model)
	{
		return number_format($model->unit_price, 2, '.', '');
	}

	protected function formatTotalCost($name, $options, $model)
	{
		return number_format($model->unit_price * $model->actual_use, 2, '.', '');
	}


}
