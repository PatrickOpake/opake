<?php

namespace Opake\ActivityLogger\Action\CaseBlock;

use Opake\ActivityLogger\Action\ModelAction;

class CaseBlockChangeAction extends ModelAction
{

	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'block' => $model->id()
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'location_id',
			'doctor_id',
			'color',
			'date_from',
			'date_to',
			'time_from',
			'time_to',
			'recurrence_every',
			'recurrence_week_days',
			'recurrence_monthly_day',
			'week_number'
		];
	}
}