<?php

namespace Opake\ActivityLogger\Action\CaseItem;

use Opake\ActivityLogger\Action\ModelAction;

class CaseCheckInAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'date' => $model->time_check_in
		];
	}

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'case_id' => $model->id()
		];
	}

	public function isNeedToSave()
	{
		return true;
	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [];
	}
}