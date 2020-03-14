<?php

namespace Opake\Formatter\Geo;

use Opake\Formatter\BaseDataFormatter;

class Country extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'name'
			],
			'fieldMethods' => [
			]
		]);
	}

}
