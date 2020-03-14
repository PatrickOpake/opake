<?php

namespace Opake\ActivityLogger\Comparer;

use Opake\ActivityLogger\ChangesComparer;

class UserChangesComparer extends ChangesComparer
{
	/**
	 * @param $result
	 * @return mixed
	 */
	protected function prepareArrayAfterCompare($result)
	{
		if (isset($result['password'])) {
			unset($result['password']);
		}

		return $result;
	}

	/**
	 * @param $field
	 * @param $newValue
	 * @param $oldValue
	 * @return bool
	 */
	protected function compareValues($field, $newValue, $oldValue)
	{
		if ($field === 'sites' || $field === 'departments') {
			return $this->compareIntArrays($newValue, $oldValue);
		}

		return $newValue == $oldValue;
	}

}