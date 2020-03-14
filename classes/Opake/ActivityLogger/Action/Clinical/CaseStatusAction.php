<?php

namespace Opake\ActivityLogger\Action\Clinical;

use Opake\ActivityLogger\Action\ModelAction;

class CaseStatusAction extends ModelAction
{

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'case_id' => $model->id()
		];
	}
}