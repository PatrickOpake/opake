<?php

namespace Opake\Model\Insurance\Data;

use Opake\Model\AbstractModel;
use Opake\Helper\TimeFormat;

class AutoAccident extends AbstractModel
{
	public $id_field = 'id';
	public $table = 'insurance_data_auto_accident';
	protected $_row = [
		'id' => null,
		'insurance_company_id' => null,
		'insurance_name' => null,
		'insurance_company_phone' => null,
		'authorization_number' => null,
		'adjuster_name' => null,
		'claim' => null,
		'adjuster_phone' => null,
		'insurance_address' => null,
		'city_id' => null,
		'state_id' => null,
		'zip' => null,
		'accident_date' => null,
		'attorney_name' => null,
		'attorney_phone' => null,
		'cms1500_payer_id' => null,
		'ub04_payer_id' => null,
		'eligibility_payer_id' => null,
		'selected_insurance_company_address_id' => null
	];

	protected $belongs_to = [
		'state' => [
			'model' => 'Geo_State',
			'key' => 'state_id'
		],
		'city' => [
			'model' => 'Geo_City',
			'key' => 'city_id'
		],
		'insurance_company' => [
			'model' => 'Insurance_Payor',
			'key' => 'insurance_company_id'
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

	public function fromArray($data)
	{
		if (isset($data->address_insurance_selected)) {
			$data->insurance_address = $data->address_insurance_selected->address;
			if (!empty($data->address_insurance_selected->id)) {
				$data->selected_insurance_company_address_id = $data->address_insurance_selected->id;
			}
			if (!empty($data->address_insurance_selected->is_new)) {
				$this->setIsNewAddressEntered(true);
			}
			unset($data->address_insurance_selected);
		}

		if (isset($data->insurance, $data->insurance->id)) {
			$data->insurance_id = $data->insurance->id;
		}

		if (isset($data->state) && $data->state) {
			$data->state_id = $data->state->id;
		}

		if (isset($data->accident_date) && $data->accident_date) {
			$data->accident_date = TimeFormat::formatToDB($data->accident_date);
		}

		if (property_exists($data, 'city')) {
			if (!empty($data->city->id)) {
				$data->city_id = $data->city->id;
			} else if (!empty($data->city->name)) {
				if (empty($data->organization_id)) {
					throw new \Exception('Can\'t add new city without ID of organization');
				}
				$model = $this->pixie->orm->get('Geo_City');
				$city = $model->addCustomRecord($data->organization_id, $data->city->state_id, $data->city->name);
				$data->city_id = $city->id();
			} else if ($data->city === null) {
				$data->city_id = null;
			}
			unset($data->city);
		}

		if (isset($data->insurance_company)) {
			if (empty($data->insurance_company->text_name)) {
				if ((is_null($data->insurance_company->id) && $data->insurance_company->name === '') || $data->insurance_company->id) {
					$data->insurance_company_id = $data->insurance_company->id;
				} else if (!empty($data->insurance_company->name)) {
					if (empty($data->organization_id)) {
						throw new \Exception('Can\'t add new insurance payor without ID of organization');
					}
					$model = $this->pixie->orm->get('Insurance_Payor');
					$insurance = $model->addCustomRecord($data->organization_id, $data->insurance_company->name);
					$data->insurance_company_id = $insurance->id();
				}
			}
			unset($data->insurance_company);
		}

		if (isset($data->organization_id)) {
			unset($data->organization_id);
		}

		return $data;
	}

	public function getValidator($key = null)
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('insurance_name')->rule('filled')->error('You must specify Auto Insurance Name');
		$validator->field('adjuster_name')->rule('filled')->error('You must specify Auto Adjust Name');
		$validator->field('claim')->rule('filled')->error('You must specify Auto Claim #');
		$validator->field('adjuster_phone')->rule('filled')->error('You must specify Auto Adjuster Phone #');
		$validator->field('insurance_address')->rule('filled')->error('You must specify Auto Insurance Address');
		$validator->field('city_id')->rule('filled')->error('You must specify Auto Insurance City');
		$validator->field('state_id')->rule('filled')->error('You must specify Auto Insurance State');
		$validator->field('zip')->rule('filled')->error('You must specify Auto Insurance ZIP');
		$validator->field('accident_date')->rule('filled')->error('You must specify Accident Date');

		return $validator;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$data['insurance_company'] = null;
		if ($this->insurance_company && $this->insurance_company->loaded()) {
			$data['insurance_company'] = $this->insurance_company->toArray();
		} else if (!empty($this->insurance_name)) {
			$data['insurance_company'] = [
				'name' => $this->insurance_name,
				'text_name' => true
			];
		}
		$data['state'] = $this->state->loaded() ? $this->state->toArray() : null;
		$data['city'] = $this->city->loaded() ? $this->city->toArray() : null;
		$data['address_insurance_selected'] = [
			'id' => $this->selected_insurance_company_address_id,
			'address' => $this->insurance_address,
			'is_new' => false
		];

		return $data;
	}

	public function fromBaseInsurance(\Opake\Model\Insurance\Data\AutoAccident $insurance)
	{
		$this->insurance_company_id = $insurance->insurance_company_id;
		$this->selected_insurance_company_address_id = $insurance->selected_insurance_company_address_id;
		$this->insurance_name = $insurance->insurance_name;
		$this->adjuster_name = $insurance->adjuster_name;
		$this->claim = $insurance->claim;
		$this->adjuster_phone = $insurance->adjuster_phone;
		$this->insurance_address = $insurance->insurance_address;
		$this->city_id = $insurance->city_id;
		$this->state_id = $insurance->state_id;
		$this->zip = $insurance->zip;
		$this->accident_date = $insurance->accident_date;
		$this->attorney_name = $insurance->attorney_name;
		$this->attorney_phone = $insurance->attorney_phone;
		$this->cms1500_payer_id = $insurance->cms1500_payer_id;
		$this->ub04_payer_id = $insurance->ub04_payer_id;
		$this->eligibility_payer_id = $insurance->eligibility_payer_id;
		$this->insurance_company_phone = $insurance->insurance_company_phone;
		$this->authorization_number = $insurance->authorization_number;
	}
}