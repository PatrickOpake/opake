<?php

namespace Opake\ActivityLogger\Formatter\Chart;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'chart_groups':
				return FormatterHelper::formatChartGroupList($this->pixie, $value);

			case 'sites':
				return FormatterHelper::formatSitesList($this->pixie, $value);

			case 'segment':
				return ucfirst($value);

			case 'include_header':
				return FormatterHelper::formatOnOff($value);

			case 'uploaded_file_id':
				return LinkFormatterHelper::formatUploadedFileLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'name' => 'Name',
			'sites' => 'Sites',
		    'chart_groups' => 'Chart Groups',
		    'segment' => 'Segment',
		    'include_header' => 'Include custom header details',
		    'uploaded_file_id' => 'File'
		];
	}
}

