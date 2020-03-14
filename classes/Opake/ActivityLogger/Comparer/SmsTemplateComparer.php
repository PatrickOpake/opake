<?php

namespace Opake\ActivityLogger\Comparer;

use Opake\ActivityLogger\ChangesComparer;
use Opake\Helper\StringHelper;

class SmsTemplateComparer extends ChangesComparer
{
	/**
	 * @param $result
	 * @return mixed
	 */
	protected function prepareArrayAfterCompare($result)
	{
		if (isset($result['schedule_msg'])) {
			$result['schedule_msg'] = StringHelper::truncate($result['schedule_msg'], 140);
		}

		if (isset($result['poc_msg'])) {
			$result['poc_msg'] = StringHelper::truncate($result['poc_msg'], 140);
		}

		return $result;
	}
}