<?php

namespace Opake\ActivityLogger\Action\Clinical;

use Opake\ActivityLogger\Action\ModelAction;

class ChecklistItemChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		$details =  [];
		$card = $model->getCard();
		if ($card->loaded()) {
			$details['card'] = 'staff';
			$details['user'] = $card->user_id;
			$details['type'] = 'Operation';
		}
		$details['text'] = $model->text;

		return $details;
	}

	protected function getSearchParams()
	{
		$model = $this->getExtractor()->getModel();
		$card = $model->getCard();

		if ($card->loaded()) {
			return [
				'case_id' => $card->case_id
			];
		}

		return null;
	}

	protected function getFieldsForCompare()
	{
		return [
			'text',
			'status'
		];
	}

}