<?php

namespace Opake\Formatter\Billing\Ledger\Patient;

use Opake\Formatter\BaseDataFormatter;

class PatientFormatter extends BaseDataFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return [
			'fields' => [
				'id',
				'mrn',
			    'first_name',
			    'last_name',
				'age',
			    'gender',
			    'home_phone',
				'dob',
			    'home_address',
			    'home_city',
			    'home_state',
			    'home_zip_code'
			],
			'fieldMethods' => [
				'id' => 'int',
				'mrn' => 'mrn',
			    'age' => 'age',
			    'gender' => 'gender',
			    'dob' => 'toDate',
			    'home_city' => 'homeCity',
			    'home_state' => 'homeState'
			]
		];
	}

	protected function formatMrn($name, $options, $model)
	{
		return $model->getFullMrn();
	}

	protected function formatAge($name, $options, $model)
	{
		return $model->getAge();
	}

	protected function formatGender($name, $options, $model)
	{
		return $model->getGender();
	}

	protected function formatHomeCity($name, $options, $model)
	{
		return ($model->home_city->loaded()) ? $model->home_city->toArray() : null;
	}

	protected function formatHomeState($name, $options, $model)
	{
		return ($model->home_state->loaded()) ? $model->home_state->toArray() : null;
	}
}