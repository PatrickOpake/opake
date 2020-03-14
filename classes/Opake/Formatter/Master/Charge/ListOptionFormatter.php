<?php

namespace Opake\Formatter\Master\Charge;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\StringHelper;

class ListOptionFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'title',
			],

			'fieldMethods' => [
				'id' => 'int',
				'title' => 'title'
			]

		]);
	}

	protected function formatTitle($name, $options, $model)
	{
		return $model->cpt . ' - ' . StringHelper::truncate($model->desc, 120);
	}
}
