<?php

namespace Opake\Formatter\User;

class FormDataFormatter extends BaseUserFormatter
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
			]

		]);
	}
}