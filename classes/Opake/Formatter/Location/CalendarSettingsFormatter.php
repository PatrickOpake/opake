<?php

namespace Opake\Formatter\Location;

class CalendarSettingsFormatter extends BaseLocationFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
				'case_color'
			]
		]);
	}

}
