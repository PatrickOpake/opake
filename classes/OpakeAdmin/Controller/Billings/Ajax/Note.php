<?php

namespace OpakeAdmin\Controller\Billings\Ajax;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class Note extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionList()
	{
		$case = $this->loadModel('Cases_Item', 'subid');


		$notes = $this->orm->get('Billing_Note')->where('and', [
			['or', ['patient_id', $case->registration->patient_id]],
			['or', ['case_id', $case->id]]
		]);

		$result = [];
		foreach ($notes->find_all() as $caseNote) {
			$result[] = $caseNote->toArray();
		}

		$this->result = $result;
	}

	public function actionListForPatient()
	{
		$patientId = $this->request->param('subid');

		$caseNotes = $this->orm->get('Billing_Note');
		$caseNotesQuery = $caseNotes->query;
		$caseNotesQuery->fields('billing_note.*');

		$caseNotesQuery->join('case', ['billing_note.case_id', 'case.id'], 'inner')
			->join('case_registration', ['case_registration.case_id', 'case.id'], 'inner')
			->where('case_registration.patient_id', $patientId);

		$result = [];
		foreach ($caseNotes->find_all() as $caseNote) {
			$result[] = $caseNote->toArray();
		}

		$this->result = $result;
	}

	public function actionSave()
	{
		$isEditAction = false;
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Billing_Note', $data->id);
			$isEditAction = true;
		} else {
			$model = $this->orm->get('Billing_Note');
			$data->organization_id = $this->request->param('id');
		}
		$user = $this->logged();

		try {
			$this->updateModel($model, $data);

			$action = ActivityRecord::ACTION_BILLING_NOTES_SAVED;
			if($isEditAction) {
				$action = ActivityRecord::ACTION_BILLING_NOTES_EDITED;
			}

			$this->pixie->activityLogger
				->newAction($action)
				->setModel($model)
				->register();

			if (!isset($data->id)) {
				$model->case->updateBillingNotesCount();
				$model->case->readBillingNotes($user->id);
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = $model->toArray();
	}

	public function actionSaveNotes()
	{
		$data = $this->getData();


		foreach ($data as $note) {
			if (isset($note->id)) {
				$model = $this->orm->get('Billing_Note', $note->id);
			} else {
				$model = $this->orm->get('Billing_Note');
			}
			$user = $this->logged();

			try {
				$this->updateModel($model, $note);
				if (!isset($note->id)) {
					$model->case->updateBillingNotesCount();
					$model->case->readBillingNotes($user->id);
				}
			} catch (\Exception $e) {
				$this->logSystemError($e);
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
		}


		$this->result = 'ok';
	}

	public function actionDelete()
	{
		$caseNote = $this->loadModel('Billing_Note', 'subid');
		$caseNote->case->reduceBillingNotesCount();

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_BILLING_NOTES_DELETED)
			->setModel($caseNote)
			->register();

		$caseNote->delete();

		$this->result = 'ok';
	}

	public function actionHasUnreadNotes()
	{
		$caseIds = $this->getData();
		$hasUnreadNotes = [];
		$user = $this->logged();

		foreach ($caseIds as $caseId) {
			$case = $this->orm->get('Cases_Item', $caseId);
			$hasUnreadNotes[$caseId] = $case->hasUnreadBillingNotesForUser($user->id);
		}

		$this->result = $hasUnreadNotes;
	}

	public function actionReadNotes()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$user = $this->logged();

		$case->readBillingNotes($user->id);
	}

	public function actionFlagNote()
	{
		$caseNote = $this->loadModel('Billing_Note', 'subid');
		$case = $caseNote->case;

		if ($case->organization_id == $this->org->id) {
			$caseNote->patient_id = $case->registration->patient_id;
			$caseNote->save();
		}

		$this->result = 'ok';
	}

	public function actionUnflagNote()
	{
		$bookingNote = $this->loadModel('Billing_Note', 'subid');
		$bookingNote->patient_id = null;
		$bookingNote->save();

		$this->result = 'ok';
	}
}
