<?php

namespace Opake\ActivityLogger\Formatter;

use Opake\ActivityLogger\ChildModelChangesHandler;
use Opake\ActivityLogger\DefaultFormatter;

class ArrayRowFormatter extends DefaultFormatter
{
	public function getFullLabel()
	{
		$data = $this->data;
		return $this->formatLabel($data['id']) . ' ' . $this->formatAction($data['action']);
	}

	protected function prepareDataBeforeFormat($data)
	{
		return (isset($data['data'])) ? $data['data'] : [];
	}

	protected function formatLabel($id)
	{
		return 'Item #' . $id;
	}

	protected function formatAction($action)
	{
		$actionsList = $this->getActionLabelsList();
		return (isset($actionsList[$action])) ? $actionsList[$action] : '';
	}

	protected function getActionLabelsList()
	{
		return [
			ChildModelChangesHandler::ACTION_ADDED => 'added',
			ChildModelChangesHandler::ACTION_CHANGED => 'changed',
			ChildModelChangesHandler::ACTION_REMOVED => 'removed'
		];
	}
}