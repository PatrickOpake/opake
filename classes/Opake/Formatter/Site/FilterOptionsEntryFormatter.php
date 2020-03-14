<?php

namespace Opake\Formatter\Site;

class FilterOptionsEntryFormatter extends BaseSiteFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
			]
		]);
	}
}