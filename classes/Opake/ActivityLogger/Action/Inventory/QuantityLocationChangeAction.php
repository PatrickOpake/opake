<?php

namespace Opake\ActivityLogger\Action\Inventory;

use Opake\ActivityLogger\Action\ModelAction;

class QuantityLocationChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'item' => $model->inventory_id
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'location_id',
			'exp_date',
			'quantity'
		];
	}
}