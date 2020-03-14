<?php

namespace Opake\Formatter\Cases\Item;


class DatePickerAlertsFormatter extends ItemFormatter
{

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'date'
			],

			'fieldMethods' => [
				'date' => ['alias',  ['alias' => 'time_start']]
			]
		]);
	}




}
