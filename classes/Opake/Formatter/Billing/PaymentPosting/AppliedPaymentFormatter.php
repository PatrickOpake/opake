<?php

namespace Opake\Formatter\Billing\PatientPosting;

use Opake\Formatter\BaseDataFormatter;

class AppliedPaymentFormatter extends BaseDataFormatter
{

	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [

			],
			'fieldMethods' => [

			]
		]);
	}
}