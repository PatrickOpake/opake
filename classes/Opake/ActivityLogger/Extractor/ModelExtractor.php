<?php

namespace Opake\ActivityLogger\Extractor;

use Opake\ActivityLogger\AbstractExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

class ModelExtractor extends AbstractExtractor
{
	/**
	 * @var AbstractModel
	 */
	protected $newModel;

	/**
	 * @var AbstractModel
	 */
	protected $oldModel;

	/**
	 * @var array
	 */
	protected $additionalInfo = [];

	/**
	 * @var ModelRelationsContainer
	 */
	protected $newModelRelations;

	/**
	 * @var ModelRelationsContainer
	 */
	protected $oldModelRelations;

	/**
	 * @param AbstractModel $model
	 */
	public function setModel($model)
	{
		$this->newModel = $model;
	}

	/**
	 * @param AbstractModel $newModel
	 * @param AbstractModel $oldModel
	 * @param ModelRelationsContainer $relationsContainer
	 */
	public function setNewAndOldModels($newModel, $oldModel, $relationsContainer = null)
	{
		$this->newModel = $newModel;
		$this->oldModel = $oldModel;

		$this->extractRelationsBeforeSave($relationsContainer);
	}

	/**
	 * @param string $fieldName
	 * @param mixed $info
	 */
	public function setAdditionalInfo($fieldName, $info)
	{
		$this->additionalInfo[$fieldName] = $info;
	}

	/**
	 * @param string $fieldName
	 * @return mixed
	 */
	public function getAdditionalInfo($fieldName)
	{
		return (isset($this->additionalInfo[$fieldName])) ? $this->additionalInfo[$fieldName] : null;
	}

	/**
	 * @return AbstractModel
	 * @throws \Exception
	 */
	public function getModel()
	{
		if (!$this->newModel) {
			throw new \Exception('Model for extracting is empty');
		}

		return $this->newModel;
	}

	/**
	 * @return bool
	 */
	public function hasModel()
	{
		return (bool) ($this->newModel);
	}

	/**
	 * @return AbstractModel
	 * @throws \Exception
	 */
	public function getOldModel()
	{
		if (!$this->oldModel) {
			throw new \Exception('Model for extracting is empty');
		}

		return $this->oldModel;
	}

	public function hasOldModel()
	{
		return (bool) ($this->oldModel);
	}

	/**
	 * @return array
	 */
	public function extractArrays()
	{
		$this->extractRelationsAfterSave();

		$newInfo = ($this->newModel) ? $this->modelToArray($this->newModel, $this->newModelRelations) : [];
		$oldInfo = ($this->oldModel) ? $this->modelToArray($this->oldModel, $this->oldModelRelations) : [];

		return [$newInfo, $oldInfo];
	}

	/**
	 * @param ModelRelationsContainer $relationsContainer
	 */
	protected function extractRelationsBeforeSave($relationsContainer = null)
	{
		if ($this->oldModel) {
			if (!$relationsContainer) {
				$relationsContainer = new ModelRelationsContainer();
			}
			$this->oldModelRelations = $relationsContainer;
			$this->extractRelations($this->oldModel, $relationsContainer);
		}
	}

	/**
	 * @param ModelRelationsContainer $relationsContainer
	 */
	protected function extractRelationsAfterSave($relationsContainer = null)
	{
		if ($this->newModel) {
			if (!$relationsContainer) {
				$relationsContainer = new ModelRelationsContainer();
			}
			$this->newModelRelations = $relationsContainer;
			$this->extractRelations($this->newModel, $relationsContainer);
		}
	}

	/**
	 * @param AbstractModel $model
	 * @param ModelRelationsContainer $relationsContainer
	 */
	protected function extractRelations($model, $relationsContainer)
	{
		if ($relationsList = $this->getRelationsList()) {
			$relationsContainer->extractRelations($model, $relationsList);
		}
	}

	/**
	 * @return array
	 */
	protected function getRelationsList()
	{
		return [];
	}

	/**
	 * @param AbstractModel $model
	 * @param ModelRelationsContainer $relationsContainer
	 * @return array
	 */
	protected function modelToArray($model, $relationsContainer)
	{
		return $model->as_array();
	}
}