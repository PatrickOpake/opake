<?php

namespace Opake\ActivityLogger\Comparer;

use Opake\ActivityLogger\ChangesComparer;
use Opake\Helper\StringHelper;

class SettingsFormsComparer extends ChangesComparer
{
	/**
	 * @param $result
	 * @return mixed
	 */
	protected function prepareArrayAfterCompare($result)
	{
		if (isset($result['own_text'])) {
			$result['own_text'] = StringHelper::truncate(strip_tags(html_entity_decode($result['own_text'])));
		}

		return $result;
	}
}