<?php

namespace Opake\ActivityLogger;

use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\Model\AbstractModel;

class ChildModelChangesHandler
{
	const ACTION_ADDED = 1;
	const ACTION_CHANGED = 2;
	const ACTION_REMOVED = 3;

	/**
	 * @var AbstractModel[]
	 */
	protected $newArrayOfModels;

	/**
	 * @var AbstractModel[]
	 */
	protected $oldArrayOfModels;

	/**
	 * @var ChangesComparer
	 */
	protected $comparer;

	/**
	 * @var AbstractExtractor
	 */
	protected $extractor;

	protected $identifierField = 'id';

	protected $useIdentifier = true;

	protected $isChangesFetched = false;

	protected $changes = [];

	/**
	 * @return \Opake\Model\AbstractModel[]
	 */
	public function getNewArrayOfModels()
	{
		return $this->newArrayOfModels;
	}

	/**
	 * @param \Opake\Model\AbstractModel[] $newArrayOfModels
	 */
	public function setNewArrayOfModels($newArrayOfModels)
	{
		$this->newArrayOfModels = $newArrayOfModels;
	}

	/**
	 * @return \Opake\Model\AbstractModel[]
	 */
	public function getOldArrayOfModels()
	{
		return $this->oldArrayOfModels;
	}

	/**
	 * @param \Opake\Model\AbstractModel[] $oldArrayOfModels
	 */
	public function setOldArrayOfModels($oldArrayOfModels)
	{
		$this->oldArrayOfModels = $oldArrayOfModels;
	}

	/**
	 * @return ChangesComparer
	 */
	public function getComparer()
	{
		return $this->comparer;
	}

	/**
	 * @param ChangesComparer $comparer
	 */
	public function setComparer($comparer)
	{
		$this->comparer = $comparer;
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
	 * @return boolean
	 */
	public function isUseIdentifier()
	{
		return $this->useIdentifier;
	}

	/**
	 * @param boolean $useIdentifier
	 */
	public function setUseIdentifier($useIdentifier)
	{
		$this->useIdentifier = $useIdentifier;
	}

	/**
	 * @return string
	 */
	public function getIdentifierField()
	{
		return $this->identifierField;
	}

	/**
	 * @param string $identifierField
	 */
	public function setIdentifierField($identifierField)
	{
		$this->identifierField = $identifierField;
	}

	public function run()
	{
		$this->tryToGetChanges();
	}

	public function isEquals()
	{
		$this->tryToGetChanges();
		return !$this->changes;
	}


	public function fetchChanges()
	{
		$this->tryToGetChanges();
		return $this->changes;
	}

	protected function tryToGetChanges()
	{
		if (!$this->isChangesFetched) {

			if (!$this->extractor) {
				$this->extractor = new ModelExtractor();
			}

			if (!$this->comparer) {
				$this->comparer = new ChangesComparer();
			}

			$oldModels = [];
			if ($this->oldArrayOfModels) {
				foreach ($this->oldArrayOfModels as $model) {
					$oldModels[$model->id()] = $model;
				}
			}

			$newModels = [];
			if ($this->newArrayOfModels) {
				foreach ($this->newArrayOfModels as $model) {
					$newModels[$model->id()] = $model;
				}
			}

			$result = [];
			foreach ($newModels as $id => $model) {
				$extractor = null;
				$collectStrategy = null;
				$action = null;
				if (!isset($oldModels[$id])) {
					$extractor = $this->getExtractor()->createNewInstance();
					$extractor->setModel($model);
					$collectStrategy = AbstractAction::CHANGES_ADD_ALL;
					$action = static::ACTION_ADDED;
				} else {
					$extractor = $this->getExtractor()->createNewInstance();
					$extractor->setNewAndOldModels($model, $oldModels[$id]);
					$collectStrategy = AbstractAction::CHANGES_COMPARE;
					$action = static::ACTION_CHANGED;
				}
				$comparer = $this->getComparer();
				$comparer->setExtractor($extractor);
				$comparer->setCollectChangesStrategy($collectStrategy);
				$changes = $comparer->fetchChanges();
				if ($collectStrategy === AbstractAction::CHANGES_ADD_ALL || $changes) {
					$result[] = $this->formatOutput($model, $action, $changes);
				}
			}

			foreach ($oldModels as $id => $model) {
				if (!isset($newModels[$id])) {
					$result[] = $this->formatOutput($model, static::ACTION_REMOVED, []);
				}
			}

			$this->changes = $result;
			$this->isChangesFetched = true;
		}
	}

	protected function formatOutput($model, $action, $data)
	{
		$identifier = ($this->useIdentifier) ? $model->{$this->identifierField} : null;
		return [
			'id' => $identifier,
			'action' => $action,
			'data' => $data
		];
	}

	public static function getArraysOfValues($models)
	{
		$result = [];
		foreach ($models as $model) {
			$result[] = $model->as_array();
		}

		return $result;
	}
}