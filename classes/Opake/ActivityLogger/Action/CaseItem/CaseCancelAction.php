<?php

namespace Opake\ActivityLogger\Action\CaseItem;

use Opake\ActivityLogger\Action\ModelAction;

class CaseCancelAction extends ModelAction
{

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'case_id' => $model->id()
		];
	}

}