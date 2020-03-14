<?php

namespace Opake\ActivityLogger\Action\CaseItem;

use Opake\ActivityLogger\Action\ModelAction;

class CaseDeleteAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
		    'dos' => $model->time_start
		];
	}

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'case_id' => $model->id()
		];
	}
}