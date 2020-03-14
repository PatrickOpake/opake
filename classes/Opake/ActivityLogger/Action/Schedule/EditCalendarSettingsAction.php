<?php

namespace Opake\ActivityLogger\Action\Schedule;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\ActivityLogger\Extractor\Schedule\SettingsExtractor;

class EditCalendarSettingsAction extends ModelAction
{
	protected function fetchDetails()
	{

	}

	/**
	 * @return array
	 */
	protected function getFieldsForCompare()
	{
		return [
			'block_timing',
			'block_overwrite',
			'colors'
		];
	}

	public function createExtractor()
	{
		return new SettingsExtractor();
	}
}