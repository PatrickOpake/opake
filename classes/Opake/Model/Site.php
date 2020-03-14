<?php

namespace Opake\Model;

use Opake\Helper\TimeFormat;

/**
 * @property \Opake\Model\Organization $organization Organization who placed on site
 */
class Site extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'site';
	protected $_row = [
		'id' => null,
		'name' => null,
		'time_create' => null,
		'description' => '',
		'comment' => '',
		'organization_id' => '',
		'address' => '',
		'city_id' => null,
		'state_id' => null,
		'zip_code' => '',
		'custom_state' => null,
		'custom_city' => null,
		'country_id' => null,
		'website' => '',
		'contact_name' => '',
		'contact_email' => '',
		'contact_phone' => '',
		'contact_fax' => '',
		'pay_name' => '',
		'pay_address' => '',
		'pay_country_id' => null,
		'pay_city_id' => null,
		'pay_state_id' => null,
		'pay_zip_code' => '',
		'pay_custom_state' => null,
		'pay_custom_city' => null,
		'chargeable' => 0,
		'npi' => '',
		'federal_tax' => '',
	    'navicure_sftp_username' => null,
	    'navicure_sftp_password' => null,
		'navicure_submitter_id' => null,
		'navicure_submitter_password' => null
	];

	protected $belongs_to = [
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id',
		],
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

	protected $has_one = [
		'alert' => [
			'model' => 'Site_Alert',
			'key' => 'site_id'
		]
	];

	protected $has_many = [
		'departments' => [
			'model' => 'department',
			'through' => 'department_site',
			'key' => 'site_id',
			'foreign_key' => 'department_id'
		],
		'locations' => [
			'model' => 'Location',
			'key' => 'site_id',
			'cascade_delete' => true
		],
		'storage' => [
			'model' => 'Location_Storage',
			'key' => 'site_id',
			'cascade_delete' => true
		],
		'users' => [
			'model' => 'user',
			'through' => 'user_site',
			'key' => 'site_id',
			'foreign_key' => 'user_id',
			'cascade_delete' => true
		],
	];

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Site\DefaultSiteFormatter'
	];

	protected $formatters = [
		'Form' => [
			'class' => '\Opake\Formatter\Site\FormSiteFormatter'
		],
	    'List' => [
		    'class' => '\Opake\Formatter\Site\ListSiteFormatter'
	    ],
		'AlertSetting' => [
			'class' => '\Opake\Formatter\Site\AlertSettingFormatter'
		],
		'FilterOptionsEntry' => [
			'class' => '\Opake\Formatter\Site\FilterOptionsEntryFormatter'
		],
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('organization_id')->rule('filled')->error('Organization undefined');
		$validator->field('name')->rule('filled')->rule('min_length', 2)->error('Invalid Site Name');
		$validator->field('name')->rule('unique', $this)->error(sprintf('Site with name %s already exists', $this->name));
		$validator->field('departments')->rule('filled')->error('You must select at least one department');
		$validator->field('website')->rule('url')->error('Invalid Web Site');
		$validator->field('contact_phone')->rule('phone')->error('Invalid Contact Phone');
		$validator->field('contact_email')->rule('email')->error('Invalid Contact Email');
		$validator->field('contact_fax')->rule('phone')->error('Invalid Contact Fax');
		$validator->field('npi')->rule('min_length', 10)->error('Length of NPI should be equal to 10');
		$validator->field('npi')->rule('max_length', 10)->error('Length of NPI should be equal to 10');
		$validator->field('npi')->rule('callback', function ($value) {
			return (bool) preg_match('#^[0-9]*$#', $value);
		})->error('NPI must contain only numeric symbols');
		return $validator;
	}

	/**
	 * Return city name
	 * @return string
	 */
	public function getCityName()
	{
		if ($this->country_id == 235) {
			return $this->city->name;
		} else {
			return $this->custom_city;
		}
	}

	/**
	 * Return pay city name
	 * @return string
	 */
	public function getPayCityName()
	{
		if ($this->pay_country_id == 235) {
			return $this->pay_city->name;
		} else {
			return $this->pay_custom_city;
		}
	}

	public function getDepartmentsCount()
	{
		return $this->departments->count_all();
	}

	public function getUsersCount()
	{
		return $this->users->count_all();
	}

	public function hasFeeSchedule()
	{
		$count = $this->pixie->orm->get('Billing_FeeSchedule_Record')
			->where('site_id', $this->id())
			->count_all();

		return ($count > 0);
	}

	public function toShortArray()
	{
		return [
			'id' => (int)$this->id,
			'name' => $this->name,
		];
	}

	public function updateChargeable($chargePrice)
	{
		$this->conn->query('update')->table($this->table)
			->data(['chargeable' => $chargePrice])
			->where('id', $this->id)
			->execute();
	}
}
