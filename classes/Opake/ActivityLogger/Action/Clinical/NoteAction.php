<?php

namespace Opake\ActivityLogger\Action\Clinical;

use Opake\ActivityLogger\Action\ModelAction;

class NoteAction extends ModelAction
{

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		$case = $model->case;

		return [
			'case_id' => $case->id()
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'text'
		];
	}
}