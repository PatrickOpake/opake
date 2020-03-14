<?php

namespace OpakeAdmin\Controller\Cases\Ajax;

use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;

class Blocking extends \OpakeAdmin\Controller\Ajax {

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionSearch()
	{
		$colorType = $this->request->get('color_type', 'doctor');
		$service = $this->services->get('cases_blocking');

		if (($this->request->get('ignore_blocks') === 'true') || $this->logged()->isSatelliteOffice()) {
			$this->result = [];
		} else {
			$search = new \OpakeAdmin\Model\Search\Cases\Blocking\Item($this->pixie);
			$blocking = $this->orm->get('Cases_Blocking_Item')->where('organization_id', $this->org->id);
			$results = $search->search($blocking, $this->request);
			$this->result = [];
			foreach ($results as $result) {
				if (!$service->cleanExpired($result, $this->org->id)) {
					$this->result[] = $result->toCalendarArray($colorType);
				}
			}
		}
	}

	public function actionBlocking()
	{
		$blocking = $this->loadModel('Cases_Blocking', 'subid');
		$this->result = $blocking->toArray();
	}

	public function actionBlockingItem()
	{
		$blockingItem = $this->loadModel('Cases_Blocking_Item', 'subid');
		$this->result = $blockingItem->toArray();
	}

	public function actionSave()
	{
		$service = $this->services->get('cases');
		$data = $this->getData();

		if ($data && isset($data->id)) {
			$model = $this->orm->get('Cases_Blocking', $data->id);
		} else {
			$model = $this->orm->get('Cases_Blocking');
			$data->organization_id = $this->request->param('id');
		}

		$service->beginTransaction();
		try {
			$model->fill($data);

			$this->checkValidationErrors($model);

			$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);
			if (!$model->loaded()) {
				$actionQueue->addAction(ActivityRecord::ACTION_CREATE_BLOCK);
			}
			$actionQueue->assign();
			$model->save();

			foreach($service->fillBlockingItems($model) as $blockItem) {
				$this->checkValidationErrors($blockItem);
				$blockItem->save();
			}
			if ($data->doctor_id) {
				$this->services->get('user')->updateUserColor($data->doctor_id, $data->color);
			}

			$actionQueue->registerActions();

		} catch (\Exception $e) {
			$this->pixie->logger->exception($e);
			$service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$service->commit();
		$this->result = ['id' => (int) $model->id];
	}

	public function actionSaveItem()
	{
		$data = $this->getData();

		if ($data && isset($data->id)) {
			$model = $this->orm->get('Cases_Blocking_Item', $data->id);
		} else {
			throw new \OpakeApi\Exception\BadRequest('Block doesn\'t exist');
		}

		try {
			$model->fill($data);
			$this->checkValidationErrors($model);

			$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);
			if ($model->loaded()) {
				$actionQueue->addAction(ActivityRecord::ACTION_EDIT_BLOCK);
			}
			$actionQueue->assign();

			$model->save();

			$actionQueue->registerActions();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = ['id' => (int) $model->id];
	}

	public function actionDelete()
	{
		$blocking = $this->loadModel('Cases_Blocking', 'subid');
		$blocking->delete();
		$this->result = 'ok';
	}

	public function actionDeleteItem()
	{
		$item = $this->loadModel('Cases_Blocking_Item', 'subid');
		$item->delete();
		$this->result = 'ok';
	}
}
