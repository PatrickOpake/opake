<?php

namespace Opake\Formatter\Cases\OperativeReport;

use Opake\Formatter\BaseDataFormatter;

class PatientChartsFormatter extends BaseDataFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id'
			]
		]);
	}

}