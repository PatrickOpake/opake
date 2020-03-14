<?php

namespace Opake\Formatter\User;

use Opake\Formatter\BaseDataFormatter;
use Opake\Helper\TimeFormat;

class BaseUserFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [

			'fields' => [
				'id',
			    'fullname',
			    'full_name',
			    'first_name',
			    'last_name',
			    'username',
			    'email',
			    'phone',
			    'case_color',
			    'image',
			    'image_default',
			    'site_ids',
			    'sites',
			    'departments',
			    'department_ids',
			    'practice_groups',
			    'is_temp_password',
			    'is_scheduled_password_change',
			    'last_change',
			    'country',
			    'country_id',
			    'state_id',
			    'state',
			    'city_id',
			    'city',
			    'custom_city',
			    'custom_state',
			    'address',
			    'profession',
			    'user_access',
			    'comment',
			    'practice_name',
			    'dea_number',
			    'dea_number_exp_date',
			    'medical_licence_number',
			    'medical_licence_number_exp_date',
			    'cds_number',
			    'cds_number_exp_date',
			    'status',
			    'photo_id',
			    'role_id',
				'profession_id',
			    'is_messaging_active',
				'chat_last_readed_id',
				'is_enabled_op_report',
			    'is_dictation_enabled',
			    'overview_display_position',
			    'phone_type',
			    'address_type',
			    'organization_id',
			    'city_name',
			    'state_name',
			    'zip_code'
			],

			'fieldMethods' => [
				'id' => 'int',
				'fullname' => [
					'modelMethod', [
						'modelMethod' => 'getFullName'
					]
				],
			    'full_name' => [
					'modelMethod', [
						'modelMethod' => 'getFullName'
					]
				],
			    'first_name' => [
				    'modelMethod', [
					    'modelMethod' => 'getFirstName'
				    ]
			    ],
			    'last_name' => [
				    'modelMethod', [
					    'modelMethod' => 'getLastName'
				    ]
			    ],
				'case_color' => [
					'modelMethod', [
						'modelMethod' => 'getCaseColor'
					]
				],
			    'image' => 'image',
			    'image_default' => 'imageDefault',
			    'site_ids' => 'deferred',
			    'sites' => 'deferred',
			    'departments' => 'deferred',
			    'department_ids' => 'deferred',
			    'practice_groups' => 'deferred',
			    'is_temp_password' => 'bool',
			    'is_scheduled_password_change' => 'bool',
			    'is_internal' => [
				    'modelMethod', [
					    'modelMethod' => 'isInternal'
				    ]
			    ],
			    'last_change' => 'lastChange',
			    'country' => 'country',
			    'country_id' => 'int',
				'state' => 'state',
				'state_id' => 'int',
				'city' => 'city',
				'city_id' => 'int',
				'profession' => 'profession',
			    'user_access' => 'role',
				'status' => [
					'modelMethod', [
						'modelMethod' => 'getStatus'
					]
				],
			    'photo_id' => 'int',
			    'role_id' => 'int',
			    'profession_id' => 'int',
			    'is_messaging_active' => 'bool',
			    'chat_last_readed_id' => 'int',
			    'is_dictation_enabled' => 'bool',
			    'is_enabled_op_report' => 'bool',
			    'overview_display_position' => 'overviewPosition',
			    'city_name' => 'cityName',
			    'state_name' => 'stateName'
			]

		]);
	}

	/**
	 * @param $data
	 * @param $fields
	 * @return mixed
	 */
	protected function prepareDeferredData($data, $fields)
	{
		if (in_array('sites', $fields) || in_array('site_ids', $fields)) {
			$sites = [];
			$siteIds = [];
			foreach ($this->model->getSites() as $site) {
				$sites[] = $site->toArray();
				$siteIds[] = (int) $site->id();
			}

			$data['sites'] = $sites;
			$data['site_ids'] = $siteIds;
		}

		if (in_array('departments', $fields) || in_array('department_ids', $fields)) {
			$departments = [];
			$departmentIds = [];
			foreach ($this->model->getDepartments() as $department) {
				$departments[] = $department->toArray();
				$departmentIds[] = (int) $department->id();
			}

			$data['departments'] = $departments;
			$data['department_ids'] = $departmentIds;
		}

		if (in_array('practice_groups', $fields)) {
			$practiceGroups = [];
			$practiceGroupIds = [];
			foreach ($this->model->practice_groups->find_all() as $practiceGroup) {
				$practiceGroupIds[] = (int) $practiceGroup->id();
				$practiceGroups[] = $practiceGroup->toArray();
			}

			$data['practice_groups'] = $practiceGroups;
		}

		return $data;
	}

	protected function formatImage($name, $options, $model)
	{
		return $model->getPhoto('tiny');
	}

	protected function formatImageDefault($name, $options, $model)
	{
		return $model->getPhoto('default');
	}

	protected function formatLastChange($name, $options, $model)
	{
		return TimeFormat::getDateTime($model->time_status_change);
	}

	protected function formatCountry($name, $options, $model)
	{
		return ($model->country && $model->country->loaded()) ? $model->country->toArray() : null;
	}

	protected function formatState($name, $options, $model)
	{
		return ($model->state && $model->state->loaded()) ? $model->state->toArray() : null;
	}

	protected function formatCity($name, $options, $model)
	{
		return ($model->city && $model->city->loaded()) ? $model->city->toArray() : null;
	}

	protected function formatRole($name, $options, $model)
	{
		return ($model->role && $model->role->loaded()) ? $model->role->toArray() : null;
	}

	protected function formatProfession($name, $options, $model)
	{
		return ($model->profession && $model->profession->loaded()) ? $model->profession->toArray() : null;
	}

	protected function formatOverviewPosition($name, $options, $model)
	{
		return (int) $model->display_settings->overview_position;
	}

	public function formatStateName($name, $options, $model)
	{
		return ($model->custom_state !== null) ? $model->custom_state :
			(($model->state && $model->state->loaded()) ? $model->state->name : null);
	}

	public function formatCityName($name, $options, $model)
	{
		return ($model->custom_city !== null) ? $model->custom_city :
			(($model->city && $model->city->loaded()) ? $model->city->name : null);
	}
}