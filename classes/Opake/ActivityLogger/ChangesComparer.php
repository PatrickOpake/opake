<?php

namespace Opake\ActivityLogger;

class ChangesComparer
{
	protected $fieldsForCompare = AbstractAction::FIELDS_ALL;
	protected $ignoredFields;

	protected $collectChangesStrategy = AbstractAction::CHANGES_COMPARE;
	protected $extractor;

	/**
	 * @return ChangesComparer
	 */
	public function createNewInstance()
	{
		return new static();
	}

	/**
	 * @return mixed
	 */
	public function getFieldsForCompare()
	{
		return $this->fieldsForCompare;
	}

	/**
	 * @param mixed $fieldsForCompare
	 */
	public function setFieldsForCompare($fieldsForCompare)
	{
		$this->fieldsForCompare = $fieldsForCompare;
	}

	/**
	 * @return mixed
	 */
	public function getIgnoredFields()
	{
		return $this->ignoredFields;
	}

	/**
	 * @param mixed $ignoredFields
	 */
	public function setIgnoredFields($ignoredFields)
	{
		$this->ignoredFields = $ignoredFields;
	}

	/**
	 * @return mixed
	 */
	public function getCollectChangesStrategy()
	{
		return $this->collectChangesStrategy;
	}

	/**
	 * @param mixed $collectChangesStrategy
	 */
	public function setCollectChangesStrategy($collectChangesStrategy)
	{
		$this->collectChangesStrategy = $collectChangesStrategy;
	}

	/**
	 * @return AbstractExtractor
	 */
	public function getExtractor()
	{
		return $this->extractor;
	}

	/**
	 * @param AbstractExtractor $extractor
	 */
	public function setExtractor($extractor)
	{
		$this->extractor = $extractor;
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function fetchChanges()
	{

		if ($this->collectChangesStrategy !== AbstractAction::CHANGES_DONT_STORE) {

			list($newData, $oldData) = $this->getExtractor()->extractArrays();

			$compareFields = $this->getFieldsForCompare();
			if ($compareFields !== AbstractAction::FIELDS_ALL) {
				if (!is_array($compareFields)) {
					throw new \Exception('Invalid compare settings format');
				}

				foreach ($newData as $fieldName => $fieldValue) {
					if (!in_array($fieldName, $compareFields)) {
						if (array_key_exists($fieldName, $newData)) {
							unset($newData[$fieldName]);
						}
						if (array_key_exists($fieldName, $oldData)) {
							unset($oldData[$fieldName]);
						}
					}
				}
			}

			if ($ignoreFields = $this->getIgnoredFields()) {
				if ($ignoreFields === AbstractAction::FIELDS_ALL) {
					$newData = [];
					$oldData = [];
				} else {
					if (!is_array($ignoreFields)) {
						throw new \Exception('Invalid compare settings format');
					}

					foreach ($ignoreFields as $fieldName) {
						if (array_key_exists($fieldName, $newData)) {
							unset($newData[$fieldName]);
						}
						if (array_key_exists($fieldName, $oldData)) {
							unset($oldData[$fieldName]);
						}
					}
				}
			}

			if ($this->collectChangesStrategy !== AbstractAction::CHANGES_ADD_ALL) {
				list($newModelArray, $dbModelArray) = $this->prepareArraysBeforeCompare($newData, $oldData);

				foreach ($dbModelArray as $field => $dbValue) {
					if (array_key_exists($field, $newModelArray) && $this->compareValues($field, $newModelArray[$field], $dbValue)) {
						unset($newModelArray[$field]);
					}
				}
			} else {
				$result = $this->prepareArraysBeforeCompare($newData, []);
				$newModelArray = $result[0];
			}

			$newModelArray = $this->prepareArrayAfterCompare($newModelArray);

			if ($newModelArray) {
				return $newModelArray;
			}
		}

		return [];
	}

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
	 * @param $result
	 * @return mixed
	 */
	protected function prepareArrayAfterCompare($result)
	{
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
		return $newValue == $oldValue;
	}

	/**
	 * @param array $oldValue
	 * @param array $newValue
	 * @return bool
	 */
	protected function compareIntArrays($newValue, $oldValue)
	{
		foreach ($oldValue as $index => $value) {
			$oldValue[$index] = (int) $value;
		}

		foreach ($newValue as $index => $value) {
			$newValue[$index] = (int) $value;
		}

		sort($oldValue);
		sort($newValue);

		return $oldValue == $newValue;
	}
}