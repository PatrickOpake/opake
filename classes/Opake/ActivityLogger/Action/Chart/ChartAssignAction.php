<?php

namespace Opake\ActivityLogger\Action\Chart;

use Opake\ActivityLogger\Extractor\Chart\ChartExtractor;

class ChartAssignAction extends ChartChangeAction
{
	protected function getFieldsForCompare()
	{
		return [
			'sites',
		    'chart_groups'
		];
	}

	/**
	 * @return ChartExtractor
	 */
	protected function createExtractor()
	{
		return new ChartExtractor();
	}
}