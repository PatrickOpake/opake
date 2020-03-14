<?php

namespace Opake\ActivityLogger\Action\Billing;


use Opake\ActivityLogger\Action\ModelAction;

class PatientStatementAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();


		return [
			'patient' => $model->getFullName()
		];
	}
}