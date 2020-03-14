<?php

namespace Opake\Formatter\Booking;

use Opake\Formatter\BaseDataFormatter;

class ItemFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), []);
	}
}