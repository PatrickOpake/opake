<?php

namespace Opake\Formatter\Organization;

use Opake\Formatter\BaseDataFormatter;

class SelectOptionsFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
			    'name'
			],

			'fieldMethods' => [
				'id' => 'int'
			]

		]);
	}
}
