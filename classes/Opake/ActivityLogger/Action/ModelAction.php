<?php

namespace Opake\ActivityLogger\Action;

use Opake\ActivityLogger\AbstractAction;
use Opake\ActivityLogger\Extractor\ModelExtractor;
use Opake\ActivityLogger\ModelRelationsContainer;
use Opake\Model\AbstractModel;

/**
 * Class ModelAction
 * @package Opake\ActivityLogger\Action
 *
 * An action instance for user activity
 *
 * Example of usage:
 *
 * $action = $this->pixie->newAction(ACTION_TYPE)
 * // if you have only new model or you don't need to compare changes in your action
 * ->setModel($newModel)
 * // if you need to compare changes
 * ->setNewAndOldModels($newModel, $oldModel)
 * // save model
 * $newModel->save();
 *
 * //register action
 * $action->register();
 */
class ModelAction extends AbstractAction
{

	/**
	 * @param AbstractModel $model
	 * @return $this
	 */
	public function setModel($model)
	{
		$this->getExtractor()->setModel($model);

		return $this;
	}

	/**
	 * @param AbstractModel $newModel
	 * @param AbstractModel $oldModel
	 * @param ModelRelationsContainer $relationsContainer
	 * @return $this
	 */
	public function setNewAndOldModels($newModel, $oldModel, $relationsContainer = null)
	{
		$this->getExtractor()->setNewAndOldModels($newModel, $oldModel, $relationsContainer);

		return $this;
	}

	/**
	 * @param string $fieldName
	 * @param mixed $info
	 *
	 * @return $this
	 */
	public function setAdditionalInfo($fieldName, $info)
	{
		$this->getExtractor()->setAdditionalInfo($fieldName, $info);

		return $this;
	}

	/**
	 * @return ModelExtractor
	 */
	public function getExtractor()
	{
		return parent::getExtractor();
	}

	/**
	 * @return ModelExtractor
	 */
	protected function createExtractor()
	{
		return new ModelExtractor();
	}

}