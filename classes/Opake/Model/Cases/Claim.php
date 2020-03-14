<?php

namespace Opake\Model\Cases;

use Opake\Model\AbstractModel;

class Claim extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'case_claim';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'provider_name' => '',
		'provider_address' => '',
		'provider_city_id' => null,
		'provider_state_id' => null,
		'provider_country_id' => null,
		'provider_zip_code' => '',
		'provider_phone' => null,
		'provider_fax' => '',
		'pay_name' => '',
		'pay_address' => '',
		'pay_country_id' => null,
		'pay_city_id' => null,
		'pay_state_id' => null,
		'pay_zip_code' => '',
		'federal_tax' => '',
		'npi' => '',

	];
	protected $belongs_to = [
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'provider_country' => [
			'model' => 'Geo_Country',
			'key' => 'provider_country_id'
		],
		'provider_state' => [
			'model' => 'Geo_State',
			'key' => 'provider_state_id'
		],
		'provider_city' => [
			'model' => 'Geo_City',
			'key' => 'provider_city_id'
		],
		'pay_country' => [
			'model' => 'Geo_Country',
			'key' => 'pay_country_id'
		],
		'pay_state' => [
			'model' => 'Geo_State',
			'key' => 'pay_state_id'
		],
		'pay_city' => [
			'model' => 'Geo_City',
			'key' => 'pay_city_id'
		],

	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	public function fromSite(\Opake\Model\Site $site)
	{
		$this->provider_name = $site->name;
		$this->provider_address = $site->address;
		$this->provider_city_id = $site->city_id;
		$this->provider_state_id = $site->state_id;
		$this->provider_country_id = $site->country_id;
		$this->provider_zip_code = $site->zip_code;
		$this->provider_phone = $site->contact_phone;
		$this->provider_fax = $site->contact_fax;

		$this->pay_name = $site->pay_name;
		$this->pay_address = $site->pay_address;
		$this->pay_country_id = $site->pay_country_id;
		$this->pay_city_id = $site->pay_city_id;
		$this->pay_state_id = $site->pay_state_id;
		$this->pay_zip_code = $site->pay_zip_code;
	}

	public function fromOrg(\Opake\Model\Organization $org)
	{
		$this->federal_tax = $org->federal_tax;
		$this->npi = $org->npi;
	}

}
