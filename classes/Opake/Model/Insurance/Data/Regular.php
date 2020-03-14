<?php

namespace Opake\Model\Insurance\Data;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class Regular extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'insurance_data_regular';
	protected $_row = [
		'id' => null,
		'insurance_id' => null,
		'insurance_company_name' => null,
		'last_name' => '',
		'first_name' => '',
		'middle_name' => '',
		'suffix' => '',
		'dob' => null,
		'gender' => null,
		'phone' => null,
		'address_insurance' => '',
		'address' => '',
		'apt_number' => '',
		'country_id' => null,
		'state_id' => null,
		'custom_state' => null,
		'city_id' => null,
		'custom_city' => null,
		'zip_code' => '',
		'relationship_to_insured' => null,
		'type' => null,
		'policy_number' => '',
		'group_number' => '',
		'order' => null,
		'provider_phone' => null,
		'insurance_verified' => null,
		'is_pre_authorization_completed' => null,
	    'authorization_or_referral_number' => null,
	    'is_accident' => null,
	    'insurance_city_id' => null,
	    'insurance_state_id' => null,
	    'insurance_zip_code' => null,
	    'is_self_funded' => null,
	    'cms1500_payer_id' => null,
	    'ub04_payer_id' => null,
	    'eligibility_payer_id' => null,
	    'selected_insurance_address_id' => null
	];

	protected $belongs_to = [
		'insurance' => [
			'model' => 'Insurance_Payor',
			'key' => 'insurance_id'
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
	    'insurance_city' => [
		    'model' => 'Geo_City',
	        'key' => 'insurance_city_id'
	    ],
	    'insurance_state' => [
		    'model' => 'Geo_State',
	        'key' => 'insurance_state_id'
	    ],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	/**
	 * @var bool
	 */
	protected $isNewAddressEntered = false;

	/**
	 * @return boolean
	 */
	public function isNewAddressEntered()
	{
		return $this->isNewAddressEntered;
	}

	/**
	 * @param boolean $isNewAddressEntered
	 */
	public function setIsNewAddressEntered($isNewAddressEntered)
	{
		$this->isNewAddressEntered = $isNewAddressEntered;
	}

	public function getValidator($key = null)
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('insurance_id')->rule('filled')->error('You must specify Insurance Company');
		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');
		$validator->field('gender')->rule('filled')->error('You must specify Gender');
		$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');
		$validator->field('address')->rule('filled')->error('You must specify Home Address');
		$validator->field('address_insurance')->rule('filled')->error('You must specify Address');
		if ($this->country_id == 235) {
			$validator->field('city_id')->rule('filled')->error('You must specify Insurance City');
			$validator->field('zip_code')->rule('filled')->error('You must specify Insurance Zip code');
			$validator->field('state_id')->rule('filled')->error('You must specify Insurance State');
		} else {
			$validator->field('custom_city')->rule('filled')->error('You must specify Insurance City');
		}
		$validator->field('country_id')->rule('filled')->error('You must specify Home Country');
		$validator->field('phone')->rule('filled')->error('You must specify Phone #');
		$validator->field('policy_number')->rule('filled')->error('You must specify Policy #');
		$validator->field('group_number')->rule('filled')->error('You must specify Group #');

		if ($key == 'filledValidationOnly') {
			return $validator;
		}

		$validator->field('phone')->rule('phone')->error('Incorrect Phone #');
		$validator->field('email')->rule('email')->error('Invalid Email Address');
		$validator->field('relationship_to_insured')->rule('filled')->error('You must specify Relationship to Patient');
		$validator->field('provider_phone')->rule('phone')->error('Incorrect Provider Phone #');

		return $validator;
	}

	public function fromArray($data)
	{
		if (isset($data->address_insurance_selected)) {
			$data->address_insurance = $data->address_insurance_selected->address;
			if (!empty($data->address_insurance_selected->id)) {
				$data->selected_insurance_address_id = $data->address_insurance_selected->id;
			}
			if (!empty($data->address_insurance_selected->is_new)) {
				$this->setIsNewAddressEntered(true);
			}
			unset($data->address_insurance_selected);
		}

		if (isset($data->insurance, $data->insurance->id)) {
			$data->insurance_id = $data->insurance->id;
		}
		if (isset($data->country) && $data->country) {
			$data->country_id = $data->country->id;
		}
		if (isset($data->state) && $data->state) {
			$data->state_id = $data->state->id;
		}

		if (isset($data->zip_code) && is_object($data->zip_code)) {
			$data->zip_code = $data->zip_code->code;
		}
		if (isset($data->dob) && $data->dob) {
			$data->dob = TimeFormat::formatToDB($data->dob);
		}
		if (isset($data->insurance_state) && $data->insurance_state) {
			$data->insurance_state_id = $data->insurance_state->id;
		}

		$cityFields = [
			'city' => 'city_id',
			'insurance_city' => 'insurance_city_id',
		];

		foreach ($cityFields as $fieldName => $idFieldName) {
			if (property_exists($data, $fieldName)) {
				if (!empty($data->$fieldName->id)) {
					$data->$idFieldName = $data->$fieldName->id;
				} else if (!empty($data->$fieldName->name)) {
					$model = $this->pixie->orm->get('Geo_City');

					$organizationId = null;
					if (isset($data->organization->id)) {
						$organizationId = $data->organization->id;
					} else if (isset($data->organization_id)) {
						$organizationId = $data->organization_id;
					} else {
						throw new \Exception('Can\'t add new city without ID of organization');
					}

					$city = $model->addCustomRecord($organizationId, $data->$fieldName->state_id, $data->$fieldName->name);
					$data->$idFieldName = $city->id();
				} else if ($data->$fieldName === null) {
					$data->$idFieldName = null;
				}
				unset($data->$fieldName);
			}
		}

		if (isset($data->insurance)) {
			if (empty($data->insurance->text_name)) {
				if ((is_null($data->insurance->id) && $data->insurance->name === '') || $data->insurance->id) {
					$data->insurance_id = $data->insurance->id;
				} else if (!empty($data->insurance->name)) {
					if (empty($data->organization_id)) {
						throw new \Exception('Can\'t add new insurance payor without ID of organization');
					}
					$model = $this->pixie->orm->get('Insurance_Payor');
					$insurance = $model->addCustomRecord($data->organization_id, $data->insurance->name);
					$data->insurance_id = $insurance->id();
				}
				unset($data->insurance);
			}
		}

		if (isset($data->organization_id)) {
			unset($data->organization_id);
		}

		return $data;
	}

	public function fromBaseInsurance(\Opake\Model\Insurance\Data\Regular $pi)
	{
		$this->insurance_id = $pi->insurance_id;
		$this->last_name = $pi->last_name;
		$this->first_name = $pi->first_name;
		$this->middle_name = $pi->middle_name;
		$this->suffix = $pi->suffix;
		$this->dob = $pi->dob;
		$this->gender = $pi->gender;
		$this->phone = $pi->phone;
		$this->address_insurance = $pi->address_insurance;
		$this->address = $pi->address;
		$this->apt_number = $pi->apt_number;
		$this->country_id = $pi->country_id;
		$this->state_id = $pi->state_id;
		$this->custom_state = $pi->custom_state;
		$this->city_id = $pi->city_id;
		$this->custom_city = $pi->custom_city;
		$this->zip_code = $pi->zip_code;
		$this->relationship_to_insured = $pi->relationship_to_insured;
		$this->type = $pi->type;
		$this->policy_number = $pi->policy_number;
		$this->group_number = $pi->group_number;
		$this->order = $pi->order;
		$this->provider_phone = $pi->provider_phone;
		$this->authorization_or_referral_number = $pi->authorization_or_referral_number;
		$this->is_accident = $pi->is_accident;
		$this->insurance_state_id = $pi->insurance_state_id;
		$this->insurance_city_id = $pi->insurance_city_id;
		$this->insurance_zip_code = $pi->insurance_zip_code;
		$this->is_self_funded = $pi->is_self_funded;
		$this->cms1500_payer_id = $pi->cms1500_payer_id;
		$this->ub04_payer_id = $pi->ub04_payer_id;
		$this->eligibility_payer_id = $pi->eligibility_payer_id;
		$this->selected_insurance_address_id = $pi->selected_insurance_address_id;
		$this->insurance_company_name = $pi->insurance_company_name;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$data['is_self_funded'] = (bool) $this->is_self_funded;

		$data['insurance'] = null;
		if ($this->insurance && $this->insurance->loaded()) {
			$data['insurance'] = $this->insurance->toArray();
		} else if (!empty($this->insurance)) {
			$data['insurance'] = [
				'name' => $this->insurance_company_name,
				'text_name' => true
			];
		}

		$data['country'] = $this->country && $this->country->loaded() ? $this->country->toArray() : null;
		$data['state'] = $this->state && $this->state->loaded() ? $this->state->toArray() : null;
		$data['city'] = $this->city && $this->city->loaded() ? $this->city->toArray() : null;
		$data['insurance_verified'] = (bool) $this->insurance_verified;
		$data['is_pre_authorization_completed'] = (bool) $this->is_pre_authorization_completed;
		$data['insurance_state'] = $this->insurance_state->loaded() ? $this->insurance_state->toArray() : null;
		$data['insurance_city'] = $this->insurance_city->loaded() ? $this->insurance_city->toArray() : null;

		$data['address_insurance_selected'] = [
			'id' => $this->selected_insurance_address_id,
			'address' => $this->address_insurance,
		    'is_new' => false
		];

		return $data;
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
}
