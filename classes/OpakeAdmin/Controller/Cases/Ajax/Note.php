<?php

namespace OpakeAdmin\Controller\Cases\Ajax;

use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\ReminderNote;
use OpakeAdmin\Form\ReminderNoteForm;

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

		$caseNotes = $this->orm->get('Cases_Note')->where('and', [
			['or', ['patient_id', $case->registration->patient_id]],
			['or', ['case_id', $case->id]]
		]);

		$result = [];
		foreach ($caseNotes->find_all() as $caseNote) {
			$result[] = $caseNote->toArray();
		}

		$this->result = $result;
	}

	public function actionListForPatient()
	{
		$patientId = $this->request->param('subid');

		$caseNotes = $this->orm->get('Cases_Note');
		$caseNotesQuery = $caseNotes->query;
		$caseNotesQuery->fields('case_note.*');

		$caseNotesQuery->join('case', ['case_note.case_id', 'case.id'], 'inner')
			->join('case_registration', ['case_registration.case_id', 'case.id'], 'inner')
			->where('case_registration.patient_id', $patientId);

		$result = [];
		foreach ($caseNotes->find_all() as $caseNote) {
			$result['cases_note'][] = $caseNote->toArray();
		}

		$bookingNotes = $this->orm->get('Booking_Note');
		$bookingNotesQuery = $bookingNotes->query;
		$bookingNotesQuery->fields('booking_note.*');

		$bookingNotesQuery->join('booking_sheet', ['booking_note.booking_id', 'booking_sheet.id'], 'inner')
			->where('booking_sheet.patient_id', $patientId);

		foreach ($bookingNotes->find_all() as $bookingNote) {
			$result['general_note'][] = $bookingNote->toArray();
		}

		$this->result = $result;
	}

	public function actionSave()
	{
		$isEditAction = false;
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Cases_Note', $data->id);
			$isEditAction = true;
		} else {
			$model = $this->orm->get('Cases_Note');
			$data->organization_id = $this->request->param('id');
		}
		$user = $this->logged();

		try {
			$this->updateModel($model, $data);

			$action = ActivityRecord::ACTION_CLINICAL_NOTES_SAVED;
			if($isEditAction) {
				$action = ActivityRecord::ACTION_CLINICAL_NOTES_EDITED;
			}

			$this->pixie->activityLogger
				->newAction($action)
				->setModel($model)
				->register();

			if (!isset($data->id)) {
				$model->case->updateNotesCount();
				$model->case->readNotes($user->id);
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = $model->toArray();
	}

	public function actionDelete()
	{
		$caseNote = $this->loadModel('Cases_Note', 'subid');
		$caseNote->case->reduceNotesCount();

		$this->pixie->activityLogger
			->newAction(ActivityRecord::ACTION_CLINICAL_NOTES_DELETED)
			->setModel($caseNote)
			->register();

		$caseNote->delete();

		$this->result = 'ok';
	}

	public function actionFlagNote()
	{
		$caseNote = $this->loadModel('Cases_Note', 'subid');
		$case = $caseNote->case;

		if ($case->organization_id == $this->org->id) {
			$caseNote->patient_id = $case->registration->patient_id;
			$caseNote->save();
		}

		$this->result = 'ok';
	}

	public function actionRemindNote()
	{
		$data = $this->getData();
		$model = $this->orm->get('ReminderNote');
		$form = new ReminderNoteForm($this->pixie, $model);
		$form->load($data);
		$form->save();

		$this->result = 'ok';
	}

	public function actionUnremindNote()
	{
		$reminderModel = $this->loadModel('ReminderNote', 'subid');
		$reminderModel->delete();

		$this->result = 'ok';
	}

	public function actionUnflagNote()
	{
		$caseNote = $this->loadModel('Cases_Note', 'subid');
		$caseNote->patient_id = null;
		$caseNote->save();

		$this->result = 'ok';
	}

	public function actionHasUnreadNotes()
	{
		$caseIds = $this->getData();
		$hasUnreadNotes = [];
		$user = $this->logged();

		foreach ($caseIds as $caseId) {
			$case = $this->orm->get('Cases_Item', $caseId);
			$hasUnreadNotes[$caseId] = $case->hasUnreadNotesForUser($user->id);
		}

		$this->result = $hasUnreadNotes;
	}

	public function actionReadNotes()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$user = $this->logged();

		$case->readNotes($user->id);
	}

}
