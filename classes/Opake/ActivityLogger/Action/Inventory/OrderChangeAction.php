<?php

namespace Opake\ActivityLogger\Action\Inventory;

use Opake\ActivityLogger\Action\ModelAction;

class OrderChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'order' => $model->id()
		];
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [];
	}
}