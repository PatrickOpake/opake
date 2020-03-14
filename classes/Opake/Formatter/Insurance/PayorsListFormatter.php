<?php

namespace Opake\Formatter\Insurance;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Insurance\AbstractType;

class PayorsListFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
			    'name',
			    'eligible_id',
			    'name',
			    'insurance_type',
			    'last_change_date',
			    'last_change_user_name',
			    'carrier_code',
			    'navicure_payor_id',
			    'navicure_eligibility_payor_id',
			    'ub04_payer_id',
			    'cms1500_payer_id',
			    'addresses'
			],

			'fieldMethods' => [
				'id' => 'int',
			    'eligible_id' => 'eligibleId',
			    'insurance_type' => 'insuranceType',
			    'city_name' => 'cityName',
			    'state_name' => 'stateName',
			    'last_change_date' => 'dateTime',
			    'last_change_user_name' => 'lastChangedUserName',
			    'addresses' => 'addresses'
			]

		]);
	}

	protected function formatAddresses($name, $options, $model)
	{
		$result = [];
		$q = $model->addresses->order_by('id')->limit(15)->find_all();
		foreach ($q as $addressModel) {
			$result[] = [
				'address' => $addressModel->address,
			    'state_name' => $addressModel->state->name,
			    'city_name' => $addressModel->city->name,
			    'phone' => $addressModel->phone,
			    'zip_code' => $addressModel->zip_code
			];
		}

		return $result;
	}


	protected function formatEligibleId($name, $options, $model)
	{
		return $model->remote_payor_id;
	}

	protected function formatInsuranceType($name, $options, $model)
	{
		$insuranceTypes = AbstractType::getInsuranceTypesList();
		return (isset($insuranceTypes[$model->insurance_type])) ? $insuranceTypes[$model->insurance_type] : '';
	}

	protected function formatCityName($name, $options, $model)
	{
		if (!empty($model->custom_city)) {
			return $model->custom_city;
		}

		if ($model->city->loaded()) {
			return $model->city->name;
		}

		return '';
	}

	protected function formatStateName($name, $options, $model)
	{
		if (!empty($model->custom_state)) {
			return $model->custom_state;
		}

		if ($model->state->loaded()) {
			return $model->state->name;
		}

		return '';
	}

	protected function formatLastChangedUserName($name, $options, $model)
	{
		if ($model->last_change_user->loaded()) {
			return $model->last_change_user->getFullName();
		}

		return '';
	}

	protected function formatDateTime($name, $options, $model)
	{
		$value = $model->{$name};
		return TimeFormat::getDateTime($value);
	}

}