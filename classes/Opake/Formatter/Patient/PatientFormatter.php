<?php

namespace Opake\Formatter\Patient;

use Opake\Formatter\BaseDataFormatter;

class PatientFormatter extends BaseDataFormatter
{
	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return [
			'fields' => BaseDataFormatter::ALL_ROW_FIELDS,
		];
	}


}