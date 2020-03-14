<?php

namespace OpakeAdmin\Controller\Cases\Ajax;

use Opake\ActivityLogger\ModelActionQueue;
use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Cases\Item;
use Opake\Model\Cases\OperativeReport;
use Opake\Model\Patient;
use Opake\Model\ReminderNote;
use OpakeAdmin\Form\Cases\CaseInfoForm;
use OpakeAdmin\Form\Cases\RegistrationForm;
use OpakeAdmin\Form\PatientMrnForm;

class Save extends \OpakeAdmin\Controller\Ajax {

	public function before() {
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionCase()
	{
		$service = $this->services->get('cases');
		$registrationId = null;

		$data = $this->getData();
		$data->organization_id = $this->org->id();
		if (isset($data->registration)) {
			$data->registration->organization_id = $this->org->id();
		}
		$isReschedule = ($this->request->post('isReschedule') == 'true');

		/** @var Item $model */
		if (isset($data->id)) {
			$model = $service->getItem($data->id);
			$isNewCase = false;
		} else {
			$model = $service->getItem();
			$model->organization_id = $this->request->param('id');
			$isNewCase = true;
		}

		try {
			$form = new CaseInfoForm($this->pixie, $model);
			$form->load((array)$data);

			$isValidRegistration = true;
			$registration = $model->registration;
			$regForm = new RegistrationForm($this->pixie, $registration);
			$regForm->load((array)$data->registration);
			if ($registration->loaded()) {
				$registrationId = $registration->id;
				$isValidRegistration = $regForm->isValid();
			}

			$isValidCase = $form->isValid();

			if(!$isValidCase || !$isValidRegistration) {
				$errors_text = '';
				$errors_text .= implode('; ', $form->getCommonErrorList());
				$errors_text .= ';' . implode('; ', $regForm->getCommonErrorList());
				throw new \Opake\Exception\ValidationError(trim($errors_text, '; '));
			}

			$booking = null;
			if ($this->request->post('bookingId')) {
				$booking = $this->orm->get('Booking', $this->request->post('bookingId'));
				if (!$booking->patient_id && $booking->booking_patient_id) {
					$patient = $this->orm->get('Patient');
					$bookingPatient = $this->orm->get('Booking_Patient', $booking->booking_patient_id);
					$patient->fromBookingPatient($bookingPatient);
					$patient->initMrn($this->org->id());
					$patient->save();
					$newInsurances = $this->copyInsurancesFromBookingPatient($bookingPatient, $patient);
					$this->createPatientActionQueue($patient);
					if (isset($data->registration->insurances)) {
						foreach ($data->registration->insurances as $insuranceData) {
							if (isset($insuranceData->selected_insurance_id)) {
								if (isset($newInsurances[$insuranceData->selected_insurance_id])) {
									$newInsuranceId = $newInsurances[$insuranceData->selected_insurance_id];
									$insuranceData->selected_insurance_id = $newInsuranceId;
								}
							}
						}
					}

					$booking->patient_id = $patient->id;
				} else {
					$patient = $this->pixie->orm->get('Patient', isset($data->patient) ? $data->patient->id : null);
				}
			} else {
				$patient = $this->pixie->orm->get('Patient', isset($data->patient) ? $data->patient->id : null);
			}
			if (!$patient->loaded() || $model->organization_id !== $patient->organization_id) {
				throw new \Opake\Exception\Ajax('Patient doesn\'t exist');
			}

			$actionQueue = $this->createCaseActionQueue($model, $patient, $isReschedule);

			if (isset($data->patient->mrn, $data->patient->mrn_year)) {
				$patientMrnForm = new PatientMrnForm($this->pixie, $patient);
				$patientMrnForm->load([
					'mrn' => $data->patient->mrn,
					'mrn_year' => $data->patient->mrn_year
				]);

				if ($patientMrnForm->isValid()) {
					$patientMrnForm->save();
				} else {
					throw new \Opake\Exception\ValidationError($patientMrnForm->getFirstErrorMessage());
				}
			}

			$isNeedToUpdateForms = $model->loaded() && $this->isCaseDocumentConditionsChanged($model);
			$model->studies_ordered = null;
			$form->save();

			if ($model->loaded()) {
				if ($isReschedule) {
					if ($model->appointment_status == \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED) {
						$model->appointment_status = \Opake\Model\Cases\Item::APPOINTMENT_STATUS_NEW;
						$model->save();
					}
					if ($caseCancellation = $model->getLastCancellation()) {
						if (isset($data->accidental_cancellation) && $data->accidental_cancellation) {
							$caseCancellation->delete();
						} else {
							$caseCancellation->rescheduled_date = $model->time_start;
							$caseCancellation->save();
						}
					}
				}
			}

			if ($registration->loaded()) {
				$regForm->save();
				$this->updateRegistrationInsurances($registration, $data->registration, $isNewCase);
				// Update patient info from registration
				$patientModel = $this->pixie->orm->get('Patient', isset($registration->patient_id) ? $registration->patient_id : null);
				$patientModel->fill($data->patient);
				$patientModel->fromRegistration($registration);
				$patientModel->save();
			}

			$this->updateMultipleFields($model, $data);

			if ($this->request->post('bookingId')) {
				$booking->status = 1;
				$booking->save();
				$booking->addCaseIdToCaseBookingList($model->id);
				if ($isNewCase) {
					$snapshot = $model->getBookingSheetSnapshot();
					if ($snapshot->loaded()) {
						$snapshot->uploaded_date = $model->time_start;
						$snapshot->save();
					}

					foreach ($booking->notes->find_all()->as_array() as $bookingNote) {
						$caseNote = $this->pixie->orm->get('Cases_Note');
						$caseNote->case_id = $model->id;
						$caseNote->user_id = $bookingNote->user_id;
						$caseNote->patient_id = $bookingNote->patient_id;
						$caseNote->time_add = $bookingNote->time_add;
						$caseNote->text = $bookingNote->text;
						$caseNote->save();

						if($bookingNote->reminder->loaded()) {
							$bookingReminder = $bookingNote->reminder;
							$reminder = $this->pixie->orm->get('ReminderNote');
							$reminder->user_id = $bookingReminder->user_id;
							$reminder->is_completed = $bookingReminder->is_completed;
							$reminder->reminder_date = $bookingReminder->reminder_date;
							$reminder->note_type = ReminderNote::TYPE_NOTE_CASES;
							$reminder->note_id = $caseNote->id();
							$reminder->save();
						}
					}
				}
			}


			if ($isNeedToUpdateForms) {
				$model->registration->updateForms();
			}

			$service->createReports($model);

			//new case
			if ($isNewCase) {
				$case_reg = $this->pixie->orm->get('Cases_Registration');
				$case_reg->case_id = $model->id;
				$case_reg->fill($data->registration);
				$case_reg->fromPatient($patient);
				$case_reg->save();

				$case_reg->initInsurancesFromPatient($patient);
				$case_reg->initForms();

				$registrationId = $case_reg->id();

				if (isset($data->registration->insurances)) {
					$this->updateRegistrationInsurances($case_reg, $data->registration, $isNewCase);
				}

				if ($booking) {
					$this->pixie->activityLogger
						->newModelActionQueue($booking)
						->setAdditionalInfo('case', $model)
						->addAction(ActivityRecord::ACTION_BOOKING_SCHEDULE)
						->assign()
						->registerActions();
				}

				$this->createBillingReports($model->id);
			}

			$actionQueue->registerActions();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = [
			'id' => (int) $model->id,
			'registration_id' => (int) $registrationId
		];
	}

	public function actionSaveInService()
	{
		$service = $this->services->get('cases');
		$data = $this->getData();

		if ($data && isset($data->id)) {
			$model = $this->orm->get('Cases_InService', $data->id);
		} else {
			$model = $this->orm->get('Cases_InService');
			$data->organization_id = $this->request->param('id');
		}

		$service->beginTransaction();
		try {
			$model->fill($data);

			$this->checkValidationErrors($model);

			$model->save();

		} catch (\Exception $e) {
			$this->pixie->logger->exception($e);
			$service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$service->commit();
		$this->result = ['id' => (int) $model->id];
	}

	public function actionSetting() {
		$data = $this->getData();

		if (isset($data->id)) {
			$model = $this->orm->get('Cases_Setting', $data->id);
		} else {
			$model = $this->orm->get('Cases_Setting');
			$data->organization_id = $this->request->param('id');
		}

		try {
			if ($data) {
				$model->fill($data);
			}

			$this->checkValidationErrors($model);

			$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);
			$actionQueue->addAction(ActivityRecord::ACTION_EDIT_CALENDAR_SETTINGS);
			$actionQueue->setAdditionalInfo('doctors', $data->doctors);
			$actionQueue->assign();

			$model->save();
			$this->services->get('user')->updateCaseColors($data->doctors);

			if (isset($data->rooms)) {
				foreach($data->rooms as $roomData) {
					if (empty($roomData->case_color)) {
						continue;
					}
					$model = $this->orm->get('Location', $roomData->id);
					if (!$model->loaded() || $model->site->organization_id !== $this->org->id) {
						continue;
					}
					$model->case_color = $roomData->case_color;
					$model->save();
				}
			}

			if (isset($data->practices)) {
				foreach($data->practices as $practiceData) {
					if (empty($practiceData->case_color)) {
						continue;
					}
					$practiceModel = $this->orm->get('PracticeGroup', $practiceData->id);
					$practiceModel->updateCaseColor($practiceData->case_color, $this->org->id);
				}
			}

			$actionQueue->registerActions();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = ['id' => (int) $model->id];
	}

	public function actionTimeLog()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$data = $this->getData();
		$service = $this->services->get('cases');

		$service->beginTransaction();
		try {
			foreach ($data as $log ) {
				$model = $this->orm->get('Cases_TimeLog')
					->where('case_id', $case->id)
					->where('stage', $log->stage)->find();
				if(!$model->loaded()) {
					$model = $this->orm->get('Cases_TimeLog');
					$log->case_id = $case->id;
				}
				$this->updateModel($model, $log);
			}
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$service->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}
		$service->commit();

		$this->result = 'ok';

	}

	public function actionCancellation()
	{
			$data = $this->getData();
			if ($data && isset($data->id)) {
				$caseCancellation = $this->orm->get('Cases_Cancellation', $data->id);
				$caseCancellation->fill($data);
				$caseCancellation->save();

				if (isset($data->cancel_status) && ($data->cancel_status == \Opake\Model\Cases\Cancellation::CANCELLED_STATUS_NO_SHOW)) {
					$caseCancellation->cancel_attempts->delete_all();
					if (!empty($data->cancel_attempts)) {
						foreach ($data->cancel_attempts as $attemptData) {
							$attemptModel = $this->orm->get('Cases_CancelAttempt', isset($attemptData->id) ? $attemptData->id : null);
							$attemptData->case_cancellation_id = $caseCancellation->id;
							$this->updateModel($attemptModel, $attemptData);
						}
					}
				}
			}

		$this->result = 'ok';
	}

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @param \Opake\Model\Patient $patient
	 * @return ModelActionQueue
	 * @throws \Exception
	 */
	protected function createCaseActionQueue($case, $patient = null, $isReschedule = false)
	{
		/** @var \Opake\ActivityLogger $logger */
		$logger = $this->pixie->activityLogger;

		$queue = $logger->newModelActionQueue($case);

		if ($isReschedule) {
			$queue->addAction(ActivityRecord::ACTION_RESCHEDULE_CASE);
		} else {
			if (!$case->loaded()) {
				$queue->addAction(ActivityRecord::ACTION_CREATE_CASE);
			} else {
				$queue->addAction(ActivityRecord::ACTION_EDIT_CASE);
			}
		}

		if ($patient && $patient->loaded()) {
			$queue->setAdditionalInfo('patient', $patient);
		}

		$queue->assign();

		return $queue;
	}

	/**
	 * @param \Opake\Model\Patient $patient
	 * @return ModelActionQueue
	 * @throws \Exception
	 */
	protected function createPatientActionQueue($patient)
	{
		$queue = $this->pixie->activityLogger->newModelActionQueue($patient);
		$queue->addAction(ActivityRecord::ACTION_BOOKING_PATIENT_CREATE);
		$queue->assign();
		$queue->registerActions();
	}

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @return bool
	 */
	protected function isCaseDocumentConditionsChanged($case)
	{
		if ($case->loaded()) {
			$service = $this->services->get('cases');
			$oldModel = $service->getItem($case->id());
			if ($case->location_id != $oldModel->location_id || $case->type_id !== $oldModel->type_id) {
				return true;
			}
		}

		return false;
	}

	protected function updateRegistrationInsurances($registration, $data, $isModelCreate = false)
	{
		if (!isset($data->insurances)) {
			return;
		}

		$updater = new \OpakeAdmin\Helper\Insurance\InputDataUpdater\CaseInsuranceUpdater(
			$registration, $data, $isModelCreate
		);

		$updater->update();
	}

	protected function copyInsurancesFromBookingPatient($bookingPatient, $patient)
	{
		$bookingPatientInsurance = $bookingPatient->insurances->find_all();
		$resultIds = [];
		if ($bookingPatientInsurance) {
			foreach ($bookingPatientInsurance as $insurance) {
				$newPatientInsurance = $this->orm->get('Patient_Insurance');
				$newPatientInsurance->fromBookingPatientInsurance($insurance);
				$newPatientInsurance->patient_id = $patient->id();
				$newPatientInsurance->save();
				$resultIds[$insurance->id()] = $newPatientInsurance->id();
			}
		}

		return $resultIds;
	}

	/**
	 * @param Item $model
	 * @param $data
	 */
	protected function updateMultipleFields($model, $data)
	{
		if (isset($data->pre_op_required_data)) {
			$model->updatePreOpRequiredData($data->pre_op_required_data);
		}
		if (isset($data->studies_ordered)) {
			$model->updateStudiesOrdered($data->studies_ordered);
		}
	}

	protected function createBillingReports($caseId)
	{
		$caseReport = $this->orm->get('Billing_Report_Cases');
		$caseReport->case_id = $caseId;
		$caseReport->organization_id = $this->org->id;
		$caseReport->save();

		$procedureReport = $this->orm->get('Billing_Report_Procedures');
		$procedureReport->case_id = $caseId;
		$procedureReport->organization_id = $this->org->id;
		$procedureReport->save();
	}
}
