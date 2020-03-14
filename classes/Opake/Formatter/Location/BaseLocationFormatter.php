<?php

namespace Opake\Formatter\Location;

use Opake\Formatter\BaseDataFormatter;

class BaseLocationFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
			],
			'fieldMethods' => [
				'id' => 'int',
			]
		]);
	}

}
