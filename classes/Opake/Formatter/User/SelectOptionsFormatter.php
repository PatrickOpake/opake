<?php

namespace Opake\Formatter\User;

use Opake\Formatter\BaseDataFormatter;

class SelectOptionsFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'fullname',
			],

			'fieldMethods' => [
				'id' => 'int',
				'fullname' => [
					'modelMethod', [
						'modelMethod' => 'getFullName'
					]
				]
			]

		]);
	}
}
