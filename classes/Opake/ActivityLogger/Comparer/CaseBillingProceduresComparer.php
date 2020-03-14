<?php

namespace Opake\ActivityLogger\Comparer;

use Opake\ActivityLogger\ChangesComparer;

class CaseBillingProceduresComparer extends ChangesComparer
{
	/**
	 * @param $field
	 * @param $newValue
	 * @param $oldValue
	 * @return bool
	 */
	protected function compareValues($field, $newValue, $oldValue)
	{
		if ($field === 'date') {
			$newValue = substr((string)$newValue, 0, 10);
			$oldValue = substr((string)$newValue, 0, 10);
		}

		return $newValue == $oldValue;
	}
}