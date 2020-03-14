<?php

namespace Opake\Formatter\Site;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;

class BaseSiteFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
				'name',
			    'description',
			    'departments_count',
			    'users_count',
			    'time_create',
			    'departments',
			    'rooms',
			    'storage',
			    'department_ids',
			    'room_names',
			    'storage_names',
			    'organization_id',
			    'comment',
			    'address',
			    'country',
			    'country_id',
			    'state',
			    'state_id',
			    'state_name',
			    'city',
			    'city_id',
			    'city_name',
			    'custom_city',
			    'custom_state',
			    'zip_code',
			    'website',
			    'contact_name',
			    'contact_phone',
			    'contact_email',
			    'contact_fax',
			    'pay_name',
			    'pay_address',
			    'pay_country',
			    'pay_country_id',
			    'pay_state',
			    'pay_state_id',
			    'pay_city',
			    'pay_city_id',
			    'pay_state_name',
			    'pay_city_name',
			    'pay_custom_city',
			    'pay_custom_state',
				'pay_zip_code',
			],

			'fieldMethods' => [
				'id' => 'int',
				'departments_count' => [
					'modelMethod', [
						'modelMethod' => 'getDepartmentsCount'
					]
				],
				'users_count' => [
					'modelMethod', [
						'modelMethod' => 'getUsersCount'
					]
				],
			    'time_create' => 'timeCreate',
				'departments' => 'deferred',
				'rooms' => 'deferred',
				'storage' => 'deferred',
			    'department_ids' => 'deferred',
			    'room_names' => 'deferred',
			    'storage_names' => 'deferred',
			    'country' => 'relationshipOne',
				'state' => 'relationshipOne',
			    'city' => 'relationshipOne',
			    'pay_country' => 'relationshipOne',
			    'pay_state' => 'relationshipOne',
			    'pay_city' => 'relationshipOne',
			    'state_name' => 'stateName',
			    'city_name' => 'cityName',
			    'pay_state_name' => 'payStateName',
			    'pay_city_name' => 'payCityName'
			]

		]);
	}

	protected function prepareDeferredData($data, $fields)
	{
		if (in_array('departments', $fields)) {
			$departments = [];
			$departmentIds = [];

			foreach ($this->model->departments->find_all() as $department) {
				$departments[] = $department->toArray();
				$departmentIds[] = (int) $department->id();
			}

			$data['departments'] = $departments;
			$data['department_ids'] = $departmentIds;
		}

		if (in_array('rooms', $fields)) {
			$rooms = [];
			$roomNames = [];

			foreach ($this->model->locations->find_all() as $room) {
				$rooms[] = $room->toArray();
				$roomNames[] = $room->name;
			}

			$data['rooms'] = $rooms;
			$data['room_names'] = $roomNames;
		}

		if (in_array('storage', $fields)) {
			$storage = [];
			$storageNames = [];

			foreach ($this->model->storage->find_all() as $location) {
				$storage[] = $location->toArray();
				$storageNames[] = $location->name;
			}

			$data['storage'] = $storage;
			$data['storage_names'] = $storageNames;
		}

		return $data;
	}

	public function formatTimeCreate($name, $options, $model)
	{
		return TimeFormat::getDateTime($model->time_create);
	}

	public function formatStateName($name, $options, $model)
	{
		return ($model->custom_state !== null) ? $model->custom_state :
			($model->state && $model->state->loaded()) ? $model->state->name : null;
	}

	public function formatCityName($name, $options, $model)
	{
		return ($model->custom_city !== null) ? $model->custom_city :
			($model->city && $model->city->loaded()) ? $model->city->name : null;
	}

	public function formatPayStateName($name, $options, $model)
	{
		return ($model->pay_custom_state !== null) ? $model->pay_custom_state :
			($model->pay_state && $model->pay_state->loaded()) ? $model->pay_state->name : null;
	}

	public function formatPayCityName($name, $options, $model)
	{
		return ($model->pay_custom_city !== null) ? $model->pay_custom_city :
			($model->pay_city && $model->pay_city->loaded()) ? $model->pay_city->name : null;
	}
}