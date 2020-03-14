<?php

namespace Opake\ActivityLogger\Formatter\Profile;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\FormatterHelper;
use Opake\ActivityLogger\LinkFormatterHelper;

class ChangesFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'country_id':
				return FormatterHelper::formatCountryName($this->pixie, $value);

			case 'status':
				return FormatterHelper::formatUserStatus($value);

			case 'profession_id':
				return FormatterHelper::formatUserProfession($this->pixie, $value);

			case 'role_id':
				return FormatterHelper::formatUserRole($this->pixie, $value);

			case 'photo_id':
				return LinkFormatterHelper::formatUploadedFileLink($this->pixie, $value);

			case 'sites':
				return FormatterHelper::formatSitesList($this->pixie, $value);

			case 'departments':
				return FormatterHelper::formatDepartmentsList($this->pixie, $value);
		}

		return $value;
	}

	protected function getIgnored()
	{
		return [
			'is_temp_password'
		];
	}

	protected function getLabels()
	{
		return [
			'id' => 'ID',
			'email' => 'Email',
			'password' => 'Password',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'address' => 'Address',
			'phone' => 'Phone',
			'country_id' => 'Country',
			'comment' => 'Comment',
			'status' => 'Status',
			'profession_id' => 'Profession',
			'role_id' => 'Role',
			'photo_id' => 'Photo',
			'sites' => 'Sites',
			'departments' => 'Departments'
		];
	}
}

