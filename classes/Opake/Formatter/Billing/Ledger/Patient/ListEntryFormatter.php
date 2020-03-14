<?php

namespace Opake\Formatter\Billing\Ledger\Patient;

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
				'name',
			    'dob',
			    'mrn',
			],
		    'fieldMethods' => [
			    'id' => 'int',
			    'name' => 'patientName',
		        'dob' => 'toDate',
		        'mrn' => 'patientMrn'
		    ]
		];
	}

	protected function formatPatientName($name, $options, $model)
	{
		return $model->getFullNameForBooking();
	}

	protected function formatPatientMrn($name, $options, $model)
	{
		return $model->getFullMrn();
	}
}