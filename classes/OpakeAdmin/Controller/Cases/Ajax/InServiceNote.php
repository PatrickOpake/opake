<?php

namespace OpakeAdmin\Controller\Cases\Ajax;

class InServiceNote extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$inService= $this->loadModel('Cases_InService', 'subid');
		$result = [];
		foreach ($inService->notes->find_all() as $inServiceNote) {
			$result[] = $inServiceNote->toArray();
		}

		$this->result = $result;
	}

	public function actionSave()
	{
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Cases_InServiceNote', $data->id);
		} else {
			$model = $this->orm->get('Cases_InServiceNote');
			$data->organization_id = $this->request->param('id');
		}
		$user = $this->logged();

		try {
			$this->updateModel($model, $data);
			if (!isset($data->id)) {
				$model->in_service->updateNotesCount();
				$model->in_service->readNotes($user->id);
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = $model->toArray();
	}

	public function actionDelete()
	{
		$caseNote = $this->loadModel('Cases_InServiceNote', 'subid');
		$caseNote->in_service->reduceNotesCount();
		$caseNote->delete();

		$this->result = 'ok';
	}

	public function actionHasUnreadNotes()
	{
		$inServiceIds = $this->getData();
		$hasUnreadNotes = [];
		$user = $this->logged();

		foreach ($inServiceIds as $inServiceId) {
			$inService = $this->orm->get('Cases_InService', $inServiceId);
			$hasUnreadNotes[$inServiceId] = $inService->hasUnreadNotesForUser($user->id);
		}

		$this->result = $hasUnreadNotes;
	}

	public function actionReadNotes()
	{
		$case = $this->loadModel('Cases_InService', 'subid');
		$user = $this->logged();

		$case->readNotes($user->id);
	}

}
