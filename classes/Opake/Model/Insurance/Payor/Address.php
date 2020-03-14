<?php

namespace Opake\Model\Insurance\Payor;

use Opake\Model\AbstractModel;

class Address extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'insurance_payor_address';
	protected $_row = [
		'id' => null,
		'payor_id' => null,
	    'address' => null,
	    'state_id' => null,
	    'city_id' => null,
	    'zip_code' => null,
	    'phone' => null
	];

	protected $belongs_to = [
		'state' => [
			'model' => 'Geo_State',
			'key' => 'state_id'
		],
		'city' => [
			'model' => 'Geo_City',
			'key' => 'city_id'
		]
	];

	protected $formatters = [
		'InsuranceFill' => [
			'class' => '\Opake\Formatter\Insurance\Address\InsuranceFillFormatter'
		]
	];

}
