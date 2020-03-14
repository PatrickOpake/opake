<?php

namespace Opake\ActivityLogger\Action\Chart;

class ChartRenameAction extends ChartChangeAction
{
	protected function getFieldsForCompare()
	{
		return [
			'name',
		];
	}
}