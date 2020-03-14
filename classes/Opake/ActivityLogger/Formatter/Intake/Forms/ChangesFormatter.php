<?php

namespace Opake\ActivityLogger\Formatter\Intake\Forms;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'status':
				return FormatterHelper::formatFormUploadStatus($value);
			case 'uploaded_file_id':
				return LinkFormatterHelper::formatUploadedFileLink($this->pixie, $value);
		}

		return $value;
	}

	protected function getLabels()
	{
		return [
			'status' => 'Status',
			'uploaded_file_id' => 'Document'
		];
	}
}