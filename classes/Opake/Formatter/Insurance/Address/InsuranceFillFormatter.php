<?php

namespace Opake\Formatter\Insurance\Address;

use Opake\Formatter\BaseDataFormatter;

class InsuranceFillFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'address',
				'state',
				'city',
				'zip_code',
				'phone'
			],
			'fieldMethods' => [
				'id' => 'int',
				'state' => 'state',
				'city' => 'city'
			]
		]);
	}

	public function formatState($name, $options, $model)
	{
		return ($model->state && $model->state->loaded()) ? $model->state->toArray() : null;
	}

	public function formatCity($name, $options, $model)
	{
		return ($model->city && $model->city->loaded()) ? $model->city->toArray() : null;
	}

}