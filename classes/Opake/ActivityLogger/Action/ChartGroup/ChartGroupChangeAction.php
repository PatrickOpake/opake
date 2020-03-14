<?php

namespace Opake\ActivityLogger\Action\ChartGroup;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\ChartGroup\ChartGroupExtractor;

class ChartGroupChangeAction extends ModelAction
{
	protected function fetchDetails()
	{
		$model = $this->getExtractor()->getModel();
		return [
			'chart_group' => $model->id()
		];
	}

	protected function getFieldsForCompare()
	{
		return [
			'name',
			'charts'
		];
	}

	/**
	 * @return ChartGroupExtractor
	 */
	protected function createExtractor()
	{
		return new ChartGroupExtractor();
	}
}