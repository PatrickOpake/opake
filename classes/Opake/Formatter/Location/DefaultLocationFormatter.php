<?php

namespace Opake\Formatter\Location;

class DefaultLocationFormatter extends BaseLocationFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
				'site_id'
			]
		]);
	}
}
