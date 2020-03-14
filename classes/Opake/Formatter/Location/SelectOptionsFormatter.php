<?php

namespace Opake\Formatter\Location;

class SelectOptionsFormatter extends BaseLocationFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name'
			]
		]);
	}
}
