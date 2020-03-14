<?php

namespace Opake\Formatter\Billing\PatientStatement;

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
				'original_dos',
				'mrn',
				'outstanding_balance',
			    'outstanding_patient_responsible_balance'
			],
			'fieldMethods' => [
				'id' => 'int',
				'original_dos' => 'toJsDate',
				'mrn' => 'patientMrn',
				'outstanding_balance' => 'money',
			    'outstanding_patient_responsible_balance' => 'money'
		 	]
		];
	}

	protected function formatPatientMrn($name, $options, $model)
	{
		return $model->getFullMrn();
	}

}