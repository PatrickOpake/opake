<?php

namespace OpakeAdmin\Form\Insurance;


use Opake\Form\AbstractForm;
use Opake\Helper\Arrays;
use Opake\Helper\TimeFormat;
use Opake\Model\Geo\City;

class PayorForm extends AbstractForm
{
	/**
	 * @var array
	 */
	protected $addresses = [];

	/**
	 * @return array
	 */
	public function getAddresses()
	{
		return $this->addresses;
	}

	/**
	 * @return array
	 */
	protected function getFields()
	{
		return [
			'name',
			'insurance_type',
			'carrier_code',
			'navicure_eligibility_payor_id',
			'ub04_payer_id',
			'cms1500_payer_id',
			'addresses',
		];
	}

	/**
	 * @param array $data
	 * @return array
	 * @throws \Exception
	 */
	protected function prepareValues($data)
	{
		$result = parent::prepareValues($data);
		if ($result['addresses']) {
			$result = Arrays::copyProperties($result, $this->addressToArray($result['addresses'][0]), [
				'address', 'state_id', 'city_id', 'zip_code', 'phone'
			]);
		}
		$this->addresses = [];
		foreach ($result['addresses'] as $address) {
			$this->addresses[] = $this->addressToArray($address);
		};
		unset($result['addresses']);

		return $result;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	protected function prepareValuesForModel($data)
	{
		$data['last_change_user_id'] = $this->pixie->auth->user()->id;
		$data['last_change_date'] = TimeFormat::formatToDBDatetime(new \DateTime());
		$data['remote_payor_id'] = null;
		$data['is_remote_payor'] = 0;
		$data['actual'] = 1;

		return $data;
	}

	/**
	 * @param \Opake\Extentions\Validate\Validator $validator
	 */
	protected function setValidationRules($validator)
	{
		$name = $this->getValueByName('name');
		$model = $this->getModel();

		$validator->field('name')->rule('filled')->error('Name is empty');
		$validator->field('name')->rule('callback', function ($val, $validator, $field) use ($name, $model) {
			$rq = $this->pixie->orm->get('Insurance_Payor')
				->where('name', $name);
			if ($model->id()) {
				$rq->where($model->id_field, '!=', $model->id());
			}

			return !$rq->find()->loaded();
		})->error('Insurance with this name already exists');
	}


	private function addressToArray($address)
	{
		$result = Arrays::copyProperties([], (array)$address, ['id', 'address', 'zip_code', 'phone']);
		$result['state_id'] = !empty($address->state) ? $address->state->id : null;

		if ($result['state_id'] && !empty($address->city) && !$address->city->id && $address->city->name) {
			/** @var City $model */
			$model = $this->pixie->orm->get('Geo_City');
			$city = $model->addCustomRecord(null, $result['state_id'], $address->city->name);
			$result['city_id'] = $city->id;
		}
		else if (!empty($address->city) && $address->city->id) {
			$result['city_id'] = $address->city->id;
		}
		else {
			$result['city_id'] = null;
		}

		return $result;
	}
}