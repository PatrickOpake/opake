<?php

namespace Opake\ActivityLogger\Action\Inventory;

use Opake\ActivityLogger\Action\ModelAction;

class InventoryItemChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'item' => $model->id()
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'barcode',
			'hcpcs',
			'auto_order',
			'is_remanufacturable',
			'is_resterilizable',
			'name',
			'manf_id',
			'desc',
			'type',
			'min_level',
			'max_level',
			'uom',
			'qty_per_uom',
			'price',
			'image_id'
		];
	}
}