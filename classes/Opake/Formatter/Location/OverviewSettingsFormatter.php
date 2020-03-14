<?php

namespace Opake\Formatter\Location;

class OverviewSettingsFormatter extends BaseLocationFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
				'overview_display_position'
			],
			'fieldMethods' => [
				'overview_display_position' => 'displayPosition'
			]
		]);
	}

	public function formatDisplayPosition($name, $options, $model)
	{
		return (int) $model->display_settings->overview_position;
	}

}
