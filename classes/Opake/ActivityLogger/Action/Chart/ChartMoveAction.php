<?php

namespace Opake\ActivityLogger\Action\Chart;

class ChartMoveAction extends ChartChangeAction
{
	protected function getFieldsForCompare()
	{
		return [
			'segment'
		];
	}
}