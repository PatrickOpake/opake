<?php

namespace Opake\ActivityLogger\Action\Chart;

use Opake\ActivityLogger\Action\ModelAction;

class ChartChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'chart' => $model->id()
		];
	}
}