<?php

namespace Opake\ActivityLogger\Action\Intake;

use Opake\ActivityLogger\Action\ModelAction;

class FormChangeAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'form' => $model->type->name
		];
	}

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'case_id' => $model->case_registration->case_id,
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'uploaded_file_id',
			'status'
		];
	}

}