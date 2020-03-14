<?php

namespace Opake\Formatter\Site;

class DefaultSiteFormatter extends BaseSiteFormatter
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
				'department_ids',
				'room_names',
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
				'pay_zip_code',
				'pay_state_name',
				'pay_city_name',
				'pay_custom_city',
				'pay_custom_state',
				'npi',
				'federal_tax',
			    'navicure_sftp_username',
			    'navicure_sftp_password',
				'navicure_submitter_id',
				'navicure_submitter_password'
			]

		]);
	}

}