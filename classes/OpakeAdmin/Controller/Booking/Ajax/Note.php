<?php

namespace OpakeAdmin\Controller\Booking\Ajax;

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
		$booking = $this->loadModel('Booking', 'subid');
		$patient_id = $this->request->get('patient_id');

		$notes = $booking->notes;
		if($patient_id) {
			$notes->where([
				['patient_id', $patient_id],
				['or', ['patient_id', 'IS NULL', $this->pixie->db->expr('')]],
			]);
		}
		$result = [];
		foreach ($notes->find_all() as $caseNote) {
			$result[] = $caseNote->toArray();
		}

		$this->result = $result;
	}

	public function actionSave()
	{
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Booking_Note', $data->id);
		} else {
			$model = $this->orm->get('Booking_Note');
			$data->organization_id = $this->request->param('id');
		}
		$user = $this->logged();

		try {
			$this->updateModel($model, $data);

			$this->pixie->activityLogger
				->newModelActionQueue($model)
				->addAction(ActivityRecord::ACTION_BOOKING_ADD_NOTE)
				->assign()
				->registerActions();

			if (!isset($data->id)) {
				$model->booking->updateNotesCount();
				$model->booking->readNotes($user->id);
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
				$model = $this->orm->get('Booking_Note', $note->id);
			} else {
				$model = $this->orm->get('Booking_Note');
			}
			$user = $this->logged();

			try {
				$this->updateModel($model, $note);
				if (!isset($note->id)) {
					$model->booking->updateNotesCount();
					$model->booking->readNotes($user->id);
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
		$caseNote = $this->loadModel('Booking_Note', 'subid');
		$caseNote->booking->reduceNotesCount();
		$caseNote->delete();

		$this->result = 'ok';
	}

	public function actionHasUnreadNotes()
	{
		$caseIds = $this->getData();
		$hasUnreadNotes = [];
		$user = $this->logged();

		foreach ($caseIds as $caseId) {
			$case = $this->orm->get('Booking', $caseId);
			$hasUnreadNotes[$caseId] = $case->hasUnreadNotesForUser($user->id);
		}

		$this->result = $hasUnreadNotes;
	}

	public function actionReadNotes()
	{
		$case = $this->loadModel('Booking', 'subid');
		$user = $this->logged();

		$case->readNotes($user->id);
	}

	public function actionFlagNote()
	{
		$bookingNote = $this->loadModel('Booking_Note', 'subid');
		$booking = $bookingNote->booking;
		if ($booking->loaded()) {

			if ($booking->organization_id == $this->org->id) {
				if (!$booking->patient_id && $booking->booking_patient_id) {
					$bookingNote->patient_id = $booking->booking_patient_id;
				} else {
					$bookingNote->patient_id = $booking->patient_id;
				}
				$bookingNote->save();
			}
		}

		$this->result = 'ok';
	}

	public function actionUnflagNote()
	{
		$bookingNote = $this->loadModel('Booking_Note', 'subid');
		$bookingNote->patient_id = null;
		$bookingNote->save();

		$this->result = 'ok';
	}
}
