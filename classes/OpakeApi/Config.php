<?php

namespace OpakeApi;

class Config extends \Opake\Config
{
	protected function getConfigAppsExcludes()
	{
		return [
			'permissions' => 'admin',
		];
	}
}