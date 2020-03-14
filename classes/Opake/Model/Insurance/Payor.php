<?php

namespace Opake\Model\Insurance;

use Opake\Model\AbstractModel;

class Payor extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'insurance_payor';
	protected $_row = [
		'id' => null,
		'name' => null,
		'remote_payor_id' => null,
		'is_remote_payor' => null,
		'actual' => null,
		'organization_id' => null,
		'insurance_type' => null,
		'address' => null,
		'address2' => null,
		'country_id' => null,
		'state_id' => null,
		'custom_state' => null,
		'city_id' => null,
		'custom_city' => null,
		'phone' => null,
		'carrier_code' => null,
		'last_change_date' => null,
		'last_change_user_id' => null,
		'zip_code' => null,
		'is_medicare' => null,
		'is_claims_enrollment_required' => null,
		'is_electronic_secondary' => null,
		'navicure_payor_id' => null,
		'navicure_eligibility_payor_id' => null,
		'era_payor_code' => null,
		'ub04_payer_id' => null,
		'cms1500_payer_id' => null,

	];

	protected $belongs_to = [
		'country' => [
			'model' => 'Geo_Country',
			'key' => 'country_id'
		],
		'state' => [
			'model' => 'Geo_State',
			'key' => 'state_id'
		],
		'city' => [
			'model' => 'Geo_City',
			'key' => 'city_id'
		],
	    'last_change_user' => [
		    'model' => 'User',
	        'key' => 'last_change_user_id'
	    ]
	];

	protected $has_many = [
		'addresses' => [
			'model' => 'Insurance_Payor_Address',
			'key' => 'payor_id'
		]
	];

	protected $formatters = [
		'PayorsList' => [
			'class' => '\Opake\Formatter\Insurance\PayorsListFormatter'
		],
		'PayorEdit' => [
			'class' => '\Opake\Formatter\Insurance\PayorsEditFormatter'
		],
	    'PayorInsuranceFill' => [
		    'class' => '\Opake\Formatter\Insurance\PayorsInsuranceFillFormatter'
	    ]
	];

	public function toArray()
	{
		return [
			'id' => $this->id(),
			'name' => $this->name,
			'carrier_code' => $this->carrier_code
		];
	}

	public function addCustomRecord($organizationId, $name)
	{
		$this->where('name', $name);
		$existedModel = $this->find();
		if ($existedModel->loaded()) {
			return $existedModel;
		}

		/** @var Payor $newModel */
		$newModel = $this->pixie->orm->get($this->model_name);
		$newModel->organization_id = $organizationId;
		$newModel->name = $name;
		$newModel->remote_payor_id = null;
		$newModel->is_remote_payor = 0;
		$newModel->actual = 1;

		$newModel->save();

		return $newModel;
	}

}
