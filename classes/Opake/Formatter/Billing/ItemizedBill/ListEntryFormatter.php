<?php

namespace Opake\Formatter\Billing\ItemizedBill;

use Opake\Formatter\BaseDataFormatter;

class ListEntryFormatter extends BaseDataFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return [
			'fields' => [
				'id',
				'first_name',
				'last_name',
				'mrn',
			],
			'fieldMethods' => [
				'id' => 'int',
				'original_dos' => 'toJsDate',
				'mrn' => 'patientMrn',
		 	]
		];
	}

	protected function formatPatientMrn($name, $options, $model)
	{
		return $model->getFullMrn();
	}

}