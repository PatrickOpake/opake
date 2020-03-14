<?php

namespace Opake\ActivityLogger\Action\CaseBlock;

use Opake\ActivityLogger\Action\ModelAction;

class CaseBlockItemChangeAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'block_item' => $model->id(),
			'block' => $model->blocking_id
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'start',
			'end',
			'location_id',
			'doctor_id',
			'color',
		];
	}
}