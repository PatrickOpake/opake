<?php

namespace Opake\Formatter\Insurance;

use Opake\Formatter\BaseDataFormatter;

class PayorsInsuranceFillFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'insurance_type',
				'carrier_code',
			    'ub04_payer_id',
			    'cms1500_payer_id',
			    'navicure_eligibility_payor_id',
			    'address_insurance_selected'
			],

			'fieldMethods' => [
				'address' => 'address',
				'country' => 'country',
			    'state' => 'state',
			    'city' => 'city',
			    'address_insurance_selected' => 'addressInsuranceSelected'
			]

		]);
	}

	public function formatAddressInsuranceSelected($name, $options, $model)
	{
		$firstAddress = $model->addresses->find();
		if ($firstAddress->loaded()) {
			return $firstAddress->getFormatter('InsuranceFill')->toArray();
		}

		return null;
	}

	public function formatPossibleAddress($name, $options, $model)
	{
		$possibleAddresses = [];
		foreach ($model->addresses->find_all() as $addressModel) {
			$possibleAddresses[] = $addressModel->getFormatter('InsuranceFillFormatter')
				->toArray();
		}

		return $possibleAddresses;
	}

}