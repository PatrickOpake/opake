<?php

namespace Opake\Formatter\Cases\Coding;

use Opake\Formatter\BaseDataFormatter;

class DiagnosisFormatter extends BaseDataFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'coding_id',
				'icd',
				'row'
			],

			'fieldMethods' => [
				'id' => 'int',
				'coding_id' => 'int',
				'icd' =>'icd',
				'row' => 'int'
			]
		]);
	}

	protected function formatIcd($name, $options, $model)
	{
		return $model->icd->toArray();
	}

}
