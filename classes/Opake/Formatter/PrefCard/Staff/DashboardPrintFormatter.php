<?php

namespace Opake\Formatter\PrefCard\Staff;

use Opake\Formatter\BaseDataFormatter;

class DashboardPrintFormatter extends BaseDataFormatter
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