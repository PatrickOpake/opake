<?php

namespace Opake\ActivityLogger\Formatter\Site;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class ChangesFormatter extends DefaultFormatter
{


	protected function formatValue($key, $value)
	{
		switch ($key) {
			case 'departments':
				return FormatterHelper::formatDepartmentsList($this->pixie, $value);
			case 'locations':
				return FormatterHelper::formatLocationsList($this->pixie, $value);
			case 'pay_country':
			case 'country':
				return FormatterHelper::formatCountryName($this->pixie, $value);
			case 'pay_state':
			case 'state':
				return FormatterHelper::formatStateName($this->pixie, $value);
			case 'pay_city':
			case 'city':
				return FormatterHelper::formatCityName($this->pixie, $value);
		}

		return $value;
	}

	protected function getAliases()
	{
		return [
			'country_id' => 'country',
			'state_id' => 'state',
			'city_id' => 'city',
			'pay_country_id' => 'pay_country',
			'pay_state_id' => 'pay_state',
			'pay_city_id' => 'pay_city',
		];
	}

	protected function getLabels()
	{
		return [
			'name' => 'Name',
			'departments' => 'Departments',
			'locations' => 'Rooms',
			'description' => 'Description',
			'comment' => 'Comments',
			'country' => 'Country',
			'state' => 'State',
			'city' => 'City',
			'zip_code' => 'Zip Code',
			'website' => 'Website',
			'contact_name' => 'Contact Name',
			'contact_phone' => 'Contact Phone',
			'contact_email' => 'Contact Email',
			'contact_fax' => 'Contact Fax Number',
			'pay_country' => 'Pay Info Country',
			'pay_state' => 'Pay Info State',
			'pay_city' => 'Pay Info City',
			'pay_zip_code' => 'Pay Info Zip Code',
		    'navicure_sftp_username' => 'Navicure SFTP Username',
		    'navicure_sftp_password' => 'Navicure SFTP Password',
			'navicure_submitter_id' => 'Navicure Submitter ID',
			'navicure_submitter_password' => 'Navicure Submitter Password'
		];
	}
}

