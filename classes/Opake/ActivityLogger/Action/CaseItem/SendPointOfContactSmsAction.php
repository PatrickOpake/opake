<?php

namespace Opake\ActivityLogger\Action\CaseItem;

use Opake\ActivityLogger\Action\ModelAction;

class SendPointOfContactSmsAction extends ModelAction
{
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