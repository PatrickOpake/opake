<?php

namespace Opake\Formatter\Analytics\Reports;


use Opake\Formatter\BaseDataFormatter;

class CustomReportsFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'user_id',
				'parent_id',
				'name',
				'columns'
			],
			'fieldMethods' => [
				'id' => 'int',
				'user_id' => 'int',
				'parent_id' => 'int',
				'columns' => 'columns'
			]
		]);
	}

	protected function formatColumns($name, $options, $model)
	{
		return explode(',', $model->columns);
	}
}
