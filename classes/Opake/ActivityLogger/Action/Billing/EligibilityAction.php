<?php

namespace Opake\ActivityLogger\Action\Billing;

use Opake\ActivityLogger\Action\ModelAction;

class EligibilityAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();


		return [
			'case_id' => $model->id(),
			'patient' => $model->registration->getFullName()
		];
	}
}