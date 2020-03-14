<?php

namespace Opake\ActivityLogger\Formatter\Site\MasterCharge;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'uploaded_file_id':
				$file = $this->pixie->orm->get('UploadedFile', $value);
				if ($file->loaded()) {
					return LinkFormatterHelper::formatLink($file->original_filename, $file->getWebPath());
				}
		}

		return $value;
	}


	protected function getLabels()
	{
		return [
			'site_name' => 'Site Name',
			'uploaded_file_id' => 'File',
			'charge_id' => 'Charge ID',
		];
	}
}

