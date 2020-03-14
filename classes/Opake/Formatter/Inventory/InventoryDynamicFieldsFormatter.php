<?php

namespace Opake\Formatter\Inventory;

use Opake\Formatter\BaseDataFormatter;

class InventoryDynamicFieldsFormatter extends BaseDataFormatter
{

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'name',
				'uom_name',
				'actual_use'
			],

			'fieldMethods' => [
				'id' => 'int',
				'uom_name' => 'UomName',
				'actual_use' => 'ActualUse',
			]
		]);
	}

	public function formatUomName($name, $options, $model)
	{
		return $model->uom->loaded() ? $model->uom->name : '';
	}

	public function formatActualUse($name, $options, $model)
	{
		return isset($model->actual_use) ? $model->actual_use : null;
	}


}
