<?php

namespace OpakeAdmin\Controller\Cards\Ajax;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class PrefSave extends \OpakeAdmin\Controller\Ajax {

	protected $_service;

	public function actionCard()
	{
		$this->_service = $this->services->get('PrefCards_Staff');
		$data = $this->getData();

		$card = $this->getCard($data);
		$this->updateModel($card, $data);

		$this->result = ['id' => (int) $card->id];
	}

	protected function updateModel($model, $data)
	{
		$this->_service->beginTransaction();
		try {
			$model->fire_events = false;

			if ($data) {
				$model->fill($data);
			}

			$this->checkValidationErrors($model);

			$queue = $this->pixie->activityLogger->newModelActionQueue($model);
			if (!$model->loaded()) {
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_ADD_PREFERENCE_CARDS);
			} else {
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_EDIT_PREFERENCE_CARDS);
			}
			$queue->assign();

			$model->save();

			if (isset($data->items)) {
				$this->_service->updateItems($model, $data->items);
			}
			if (isset($data->notes)) {
				$this->_service->updateNotes($model, $data->notes);
			}

			$queue->registerActions();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->_service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$this->_service->commit();
		$model->fire_events = true;
		$this->pixie->events->fireEvent('save.' . $model->table, $model);

		$this->result = ['id' => (int) $model->id];
	}

	protected function getCard($data)
	{
		if (isset($data->id)) {
			$model = $this->_service->getItem($data->id);
			$user = $this->logged();
			if (!$user->isInternal()) {
				if (isset($model->organization_id)) {
					$organization_id = $model->organization_id;
				} else {
					$organization_id = $model->user->organization_id;
				}
				if (!$model->loaded() || $organization_id != $user->organization_id) {
					throw new \Opake\Exception\PageNotFound();
				}
			}
		} else {
			$model = $this->_service->getItem();
		}
		return $model;
	}

}
