<?php

namespace Opake\ActivityLogger\Comparer;

use Opake\ActivityLogger\AbstractAction;
use Opake\ActivityLogger\ChangesComparer;
use Opake\ActivityLogger\ChildModelChangesHandler;
use Opake\ActivityLogger\Extractor\ArrayExtractor;

class OrganizationComparer extends ChangesComparer
{
	/**
	 * @var array
	 */
	protected $permissionChanges = [];

	/**
	 * @param $newData
	 * @param $oldData
	 * @return array
	 */
	protected function prepareArraysBeforeCompare($newData, $oldData)
	{
		return [$newData, $oldData];
	}

	/**
	 * @param $field
	 * @param $newValue
	 * @param $oldValue
	 * @return bool
	 */
	protected function compareValues($field, $newValue, $oldValue)
	{
		if ($field === 'permissions') {
			$comparer = new ChangesComparer();
			$comparer->setCollectChangesStrategy(AbstractAction::CHANGES_COMPARE);
			$extractor = new ArrayExtractor();
			$extractor->setNewAndOldArrays($newValue, $oldValue);
			$comparer->setExtractor($extractor);
			$changes = $comparer->fetchChanges();
			$this->permissionChanges = $changes;

			return !$changes;
		}

		return $newValue == $oldValue;
	}

	protected function prepareArrayAfterCompare($result)
	{
		if (isset($result['permissions'])) {
			$result['permissions'] = $this->permissionChanges;
		}

		return $result;
	}

}
