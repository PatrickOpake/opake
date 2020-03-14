<?php

namespace Opake\Formatter\Insurance;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;
use Opake\Model\Insurance\AbstractType;

class PayorsEditFormatter extends PayorsListFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
				'name',
				'insurance_type',
				'carrier_code',
				'navicure_eligibility_payor_id',
				'ub04_payer_id',
				'cms1500_payer_id',
				'addresses'
			],

			'fieldMethods' => [
				'id' => 'int',
				'addresses' => 'addresses'
			]

		]);
	}

	protected function formatAddresses($name, $options, $model)
	{
		$result = [];
		$q = $model->addresses->order_by('id')->find_all();
		foreach ($q as $addressModel) {
			$result[] = [
				'id'=>  (int)$addressModel->id,
				'address' => $addressModel->address,
				'state' => $addressModel->state->toArray(),
				'city' => $addressModel->city->toArray(),
				'phone' => $addressModel->phone,
				'zip_code' => $addressModel->zip_code
			];
		}

		return $result;
	}

	protected function formatInsuranceType($name, $options, $model)
	{
		return $model->insurance_type;
	}

}