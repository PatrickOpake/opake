<?php

namespace Opake\ActivityLogger\Action\Chart;

class ChartUploadAction extends ChartChangeAction
{
	protected function getFieldsForCompare()
	{
		return [
		    'name',
		    'uploaded_file_id'
		];
	}
}