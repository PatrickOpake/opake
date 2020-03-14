<?php

namespace OpakeAdmin\Controller\Cards\Ajax;

class Save extends \OpakeAdmin\Controller\Ajax {

	protected $_service;

	public function actionStaff() {
		$this->_service = $this->services->get('Cards_Staff');
		$this->_service->setLogChanges(true);
		$data = $this->getData();
		$card = $this->getCard($data);

		$this->updateModel($card, $data);
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
			$model->save();

			$isNewCard = false;
			if (!isset($data->id)) {
				$isNewCard = true;
			}

			if (isset($data->items)) {
				$this->_service->updateItems($model, $data->items, $isNewCard);
			}
			if (isset($data->notes)) {
				$this->_service->updateNotes($model, $data->notes);
			}

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->_service->rollback();
			throw new \Exception($e->getMessage());
		}
		$this->_service->commit();
		$model->fire_events = true;
		$this->pixie->events->fireEvent('save.' . $model->table, $model);

		$this->result = $model->toArray();
	}

	protected function getCard($data = null)
	{
		if (isset($data->id)) {
			$model = $this->_service->getItem($data->id);
		} else {
			$model = $this->_service->getItem();
		}
		return $model;
	}

}
