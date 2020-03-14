<?php

namespace Opake\ActivityLogger;

use Opake\ActivityLogger\Action\ModelAction;
use Opake\Model\AbstractModel;
use PHPixie\ORM\Model;

/**
 * Class ModelActionQueue
 * @package Opake\ActivityLogger
 *
 * Queue of actions for logging user activity
 *
 * Example of usage:
 * $queue = $this->pixie->activityLogger->newModelActionQueue($model)
 * ->addAction(...)
 * ->addAction(...)
 * ->assign();
 * ...
 * //save model and related data
 * $model->save()
 * ...
 * //if everything is ok, register the actions
 * $queue->registerActions();
 *
 * Note that you can use just $this->pixie->activityLogger->newAction(ActionName)
 * for simple actions, if your action doesn't require fetching old model for compare
 * or if you need to assign old model manually.
 *
 */
class ModelActionQueue
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var ModelAction[]
	 */
	protected $actions = [];

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
	 * @var bool
	 */
	protected $isDataAssigned = false;

	/**
	 * @var bool
	 */
	protected $isDebugEnabled = false;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
		$this->isDebugEnabled = $pixie->config->get('app.debugmode');
	}

	/**
	 * @param Model $model Non-saved model
	 * @return $this
	 */
	public function setNotSavedModel($model)
	{
		$this->newModel = $model;
		$this->isDataAssigned = false;

		return $this;
	}

	/**
	 * @param string $fieldName
	 * @param mixed $info
	 * @return $this
	 */
	public function setAdditionalInfo($fieldName, $info)
	{
		$this->additionalInfo[$fieldName] = $info;
		$this->isDataAssigned = false;

		return $this;
	}

	/**
	 * @param int $actionType
	 * @return $this
	 * @throws \Exception
	 */
	public function addAction($actionType)
	{
		if ($this->isDataAssigned) {
			$this->pixie->logger->warning('Data is already assigned, trying to add action after assign: ' . $actionType);
		}

		$this->actions[] = $this->pixie->activityLogger->newAction($actionType);
		$this->isDataAssigned = false;

		return $this;
	}

	/**
	 * @param ModelAction $action
	 * @return $this
	 */
	public function addActionObject(ModelAction $action)
	{
		if ($this->isDataAssigned) {
			$this->pixie->logger->warning('Data is already assigned, trying to add action after assign: ' . $action->getActionType());
		}

		$this->actions[] = $action;
		$this->isDataAssigned = false;

		return $this;
	}

	/**
	 * Always call assign before saving of model
	 *
	 * Call assign() to set data and fetch old model relations before it will be replaced by new ones
	 * If you don't call this method before saving of new model, it will set model to actions anyway
	 * but relations will be always equals on comparing step.
	 *
	 * @throws \Exception
	 */
	public function assign()
	{
		if (!$this->isDataAssigned) {

			try {

				$isOldModelRequired = false;
				foreach ($this->actions as $action) {
					if ($action->getCollectChangesStrategy() === ModelAction::CHANGES_COMPARE) {
						$isOldModelRequired = true;
						break;
					}
				}

				//if some of actions require old model
				if ($isOldModelRequired) {
					if ($this->newModel && $this->newModel->loaded() && !$this->oldModel) {
						$oldModel = $this->pixie->orm->get($this->newModel->model_name, $this->newModel->id());
						if ($oldModel->loaded()) {
							$this->oldModel = $oldModel;
						}
					}
				}

				//prevent second execution, use common relations container
				$relationsContainer = new ModelRelationsContainer();
				foreach ($this->actions as $action) {
					if ($action instanceof ModelAction) {
						if ($this->additionalInfo) {
							foreach ($this->additionalInfo as $filedName => $data) {
								$action->setAdditionalInfo($filedName, $data);
							}
						}
						if ($this->oldModel) {
							$action->setNewAndOldModels($this->newModel, $this->oldModel, $relationsContainer);
						} else {
							$action->setModel($this->newModel);
						}
					}
				}

				$this->isDataAssigned = true;

			} catch (\Exception $e) {
				if ($this->isDebugEnabled) {
					throw $e;
				} else {
					$this->pixie->logger->exception($e);
					$this->isDataAssigned = true;
				}
			}
		}

		return $this;
	}


	/**
	 * @return $this
	 * @throws \Exception
	 */
	public function registerActions()
	{
		$this->assign();

		try {
			foreach ($this->actions as $action) {
				$action->register();
			}
		} catch (\Exception $e) {
			if ($this->isDebugEnabled) {
				throw $e;
			} else {
				$this->pixie->logger->exception($e);
			}
		}

		return $this;
	}
}