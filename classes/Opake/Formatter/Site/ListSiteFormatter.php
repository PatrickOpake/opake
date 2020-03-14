<?php

namespace Opake\Formatter\Site;

class ListSiteFormatter extends BaseSiteFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
				'description',
				'departments_count',
				'users_count',
			]

		]);
	}
}