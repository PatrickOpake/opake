<?php

namespace Opake\ActivityLogger\Formatter\Organization;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'permissions':
				return FormatterHelper::formatKeyValue($this->pixie, $value, '\Opake\ActivityLogger\Formatter\Organization\Permissions\PermissionsFormatter');

			case 'status':
				return FormatterHelper::formatUserStatus($value);

			case 'country_id':
				return FormatterHelper::formatCountryName($this->pixie, $value);
		}


		return $value;
	}

	protected function getLabels()
	{
		return [
			'name' => 'Name',
			'status' => 'Status',
			'address' => 'Address',
			'country_id' => 'Country',
			'website' => 'Web Site',
			'contact_name' => 'Administrator Info Name',
			'contact_email' => 'Administrator Info Email',
			'contact_phone' => 'Administrator Info Phone',
			'comment' => 'Comments',
			'federal_tax' => 'Tax ID / EIN Number',
			'npi' => 'NPI',
			'permissions' => 'Permissions'
		];
	}
}
