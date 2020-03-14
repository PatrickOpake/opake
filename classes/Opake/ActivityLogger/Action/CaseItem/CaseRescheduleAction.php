<?php

namespace Opake\ActivityLogger\Action\CaseItem;

use Opake\ActivityLogger\Action\ModelAction;

class CaseRescheduleAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();

		$data = [
			'new_dos' => $model->time_start,
		];

		if ($this->getExtractor()->hasOldModel()) {
			$oldModel = $this->getExtractor()->getOldModel();
			$data['old_dos'] = $oldModel->time_start;
		}

		return $data;
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