<?php

namespace OpakeAdmin\Controller\Cases\OperativeReports\Ajax;

class Note extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$report = $this->loadModel('Cases_OperativeReport', 'subid');
		$result = [];
		foreach ($report->notes->find_all() as $reportNote) {
			$result[] = $reportNote->toArray();
		}

		$this->result = $result;
	}

	public function actionSave()
	{
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Cases_OperativeReport_Note', $data->id);
		} else {
			$model = $this->orm->get('Cases_OperativeReport_Note');
			$data->organization_id = $this->request->param('id');
		}
		$user = $this->logged();

		try {
			$this->updateModel($model, $data);
			if (!isset($data->id)) {
				$model->report->updateNotesCount();
				$model->report->readNotes($user->id);
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = $model->toArray();
	}

	public function actionDelete()
	{
		$reportNote = $this->loadModel('Cases_OperativeReport_Note', 'subid');
		$reportNote->report->reduceNotesCount();
		$reportNote->delete();

		$this->result = 'ok';
	}

	public function actionHasUnreadNotes()
	{
		$reportIds = $this->getData();
		$hasUnreadNotes = [];
		$user = $this->logged();

		foreach ($reportIds as $reportId) {
			$report = $this->orm->get('Cases_OperativeReport', $reportId);
			$hasUnreadNotes[$reportId] = $report->hasUnreadNotesForUser($user->id);
		}

		$this->result = $hasUnreadNotes;
	}

	public function actionReadNotes()
	{
		$case = $this->loadModel('Cases_OperativeReport', 'subid');
		$user = $this->logged();

		$case->readNotes($user->id);
	}

}
