<?php

namespace Opake\ActivityLogger\Comparer;

use Opake\ActivityLogger\ChangesComparer;
use Opake\ActivityLogger\ChildModelChangesHandler;

class ChildModelsChangesComparer extends ChangesComparer
{
	/**
	 * @var ChildModelChangesHandler
	 */
	protected $handlers = [];

	/**
	 * @param $newData
	 * @param $oldData
	 * @return array
	 */
	protected function prepareArraysBeforeCompare($newData, $oldData)
	{
		$this->initHandlers($newData, $oldData);

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
		if ($this->hasChildHandler($field)) {
			$handler = $this->getChildHandler($field);
			return $handler->isEquals();
		}

		return $newValue == $oldValue;
	}

	/**
	 * @param array $result
	 * @return array
	 */
	protected function prepareArrayAfterCompare($result)
	{
		foreach ($this->handlers as $fieldName => $handler) {
			if (isset($result[$fieldName])) {
				$result[$fieldName] = $handler->fetchChanges();
			}
		}

		return $result;
	}

	protected function initHandlers($newData, $oldData)
	{
		foreach ($this->getChildModelFields() as $fieldName => $options) {
			$handler = new ChildModelChangesHandler();
			if (isset($options['comparerClass'])) {
				$comparerClass = $options['comparerClass'];
				$comparer = new $comparerClass();
			} else {
				$comparer = new ChangesComparer();
			}
			if (isset($options['fieldsForCompare'])) {
				$comparer->setFieldsForCompare($options['fieldsForCompare']);
			}
			if (isset($options['ignoreFields'])) {
				$comparer->setIgnoredFields($options['ignoreFields']);
			}
			$handler->setComparer($comparer);

			if (isset($newData[$fieldName])) {
				$handler->setNewArrayOfModels($newData[$fieldName]);
			}
			if (isset($oldData[$fieldName])) {
				$handler->setOldArrayOfModels($oldData[$fieldName]);
			}
			if (isset($options['extractorClass'])) {
				$extractorClass = $options['extractorClass'];
				$extractor = new $extractorClass();
				$handler->setExtractor($extractor);
			}
			$this->handlers[$fieldName] = $handler;
		}

	}

	/**
	 * @param string $fieldName
	 * @return bool
	 */
	protected function hasChildHandler($fieldName)
	{
		return (isset($this->handlers[$fieldName]));
	}

	/**
	 * @param string $fieldName
	 * @return ChildModelChangesHandler
	 * @throws \Exception
	 */
	protected function getChildHandler($fieldName)
	{
		if (!isset($this->handlers[$fieldName])) {
			throw new \Exception('Child model handler for field ' . $fieldName . ' is not initialized');
		}

		return $this->handlers[$fieldName];
	}

	/**
	 * @return array
	 */
	protected function getChildModelFields()
	{
		return [];
	}
}