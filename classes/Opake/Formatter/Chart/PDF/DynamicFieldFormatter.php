<?php

namespace Opake\Formatter\Chart\PDF;

use Opake\Formatter\BaseDataFormatter;

class DynamicFieldFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
				'fields' => [
					'id',
					'page',
					'key',
					'x',
					'y',
					'width',
					'height'
				],
				'fieldMethods' => [
					'id' => 'int',
					'key' => 'key'
				]
		]);
	}

	protected function formatKey($name, $options, $model)
	{
		return $model->name;
	}

}
