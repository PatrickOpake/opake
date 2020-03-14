<?php

namespace OpakeAdmin\Controller\Booking;

use Opake\ActivityLogger\ModelActionQueue;
use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use Opake\Helper\Config;
use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Booking;
use Opake\Model\BookingSheetTemplate;
use Opake\Model\Patient;
use OpakeAdmin\Form\Booking\BookingSaveForm;
use OpakeAdmin\Form\Booking\BookingSubmitForm;
use OpakeAdmin\Form\Booking\PatientForm;
use OpakeAdmin\Form\PatientMrnForm;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$items = [];

		$model = $this->orm->get('Booking')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Booking($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->getFormatter('BookingQueue')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionBooking()
	{
		$model = $this->loadModel('Booking', 'subid');
		$this->result = $model->toArray();
	}

	public function actionBookingWithTemplate()
	{
		$model = $this->loadModel('Booking', 'subid');
		$templateData = null;
		if ($model->template_snapshot->loaded()) {
			$templateData = $model->template_snapshot
				->getFormatter('TemplateBookingSheet')
				->toArray();
		}
		if ($templateData === null) {
			$templateData = BookingSheetTemplate::createDefaultBookingSheetTemplate()
				->getFormatter('TemplateBookingSheet')
				->toArray();
		}

		$this->result = [
			'booking' => $model->toArray(),
		    'template' => $templateData
		];
	}

	public function actionSave()
	{
		$service = $this->services->get('bookings');
		$data = $this->getData();

		if (!$data) {
			throw new BadRequest('Bad Request');
		}
		$isBookingPatient = false;

		$model = $this->orm->get('Booking', isset($data->id) ? $data->id : null);
		if (isset($data->patient->id)) {
			if (!isset($data->patient->mrn)) {
				$patientModel = $this->orm->get('Booking_Patient', $data->patient->id);
				$isBookingPatient = true;
			} else {
				$patientModel = $this->orm->get('Patient', $data->patient->id);
			}
		} else {
			$patientModel = $this->orm->get('Booking_Patient');
			$isBookingPatient = true;
		}

		if (!$patientModel->loaded()) {
			$patientModel->organization_id = $this->org->id;
			$data->patient->organization_id = $this->org->id;
		} else if ($patientModel->organization_id !== $this->org->id) {
			throw new \Opake\Exception\Ajax('Patient doesn\'t exist');
		}

		if (!$model->loaded()) {
			$model->organization_id = $this->org->id;
			$data->organization_id = $this->org->id;
		} elseif ($model->organization_id !== $this->org->id) {
			throw new \Opake\Exception\Ajax('Booking doesn\'t exist');
		}

		$model->beginTransaction();
		try {

			$data->organization_id = $this->org->id();
			if (isset($data->patient)) {
				$data->patient->organization_id = $this->org->id();
			}

			if (!$isBookingPatient) {
				$form = new PatientMrnForm($this->pixie, $patientModel);
				$form->load((array)$data->patient);
				if (!$form->isValid()) {
					throw new \Opake\Exception\ValidationError($form->getFirstErrorMessage());
				}
			}

			$isModelCreate = (!$model->loaded());

			$actionQueue = $this->createBookingActionQueue($model);

			$patientForm = new PatientForm($this->pixie, $patientModel);
			$patientForm->load((array)$data->patient);
			if (!$patientForm->isValid()) {
				$this->result = [
					'success' => false,
					'errors' => $patientForm->getCommonErrorList()
				];
				return;
			}
			$patientForm->save();
			if ($isBookingPatient) {
				$model->booking_patient_id = $patientModel->id();
			} else {
				$model->patient_id = $patientModel->id();
			}

			if (isset($data->is_submit)) {
				$bookingForm = new BookingSubmitForm($this->pixie, $model);
				$model->status = \Opake\Model\Booking::STATUS_SUBMITTED;
			} else {
				$bookingForm = new BookingSaveForm($this->pixie, $model);
			}

			$bookingForm->load((array)$data);
			if (!$bookingForm->isValid()) {
				$this->result = [
					'success' => false,
					'errors' => $bookingForm->getCommonErrorList()
				];
				return;
			}
			$bookingForm->save();

			$this->updateBookingInsurances($model, $data, $patientModel, $isModelCreate);
			$this->updateMultipleFields($model, $data);
			if ($isModelCreate) {
				$this->saveBookingTemplateSnapshot($model, $data);
			}

			if (isset($data->is_submit)) {
				$this->createBookingSheetSnapshot($model, $this->getData());
			}

			$actionQueue->registerActions();

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$model->rollback();
			$this->result = [
				'success' => false,
				'errors' => [$e->getMessage()]
			];
			return;
		}
		$model->commit();

		$this->result = [
			'success' => true,
			'id' => (int)$model->id,
			'patient_id' => (int)$model->patient->id
		];
	}

	public function actionRemove()
	{
		$this->checkAccess('booking', 'delete');
		$model = $this->loadModel('Booking', 'subid');
		$model->delete();

		$this->pixie->activityLogger
			->newModelActionQueue($model)
			->addAction(ActivityRecord::ACTION_BOOKING_REMOVE)
			->assign()->registerActions();
	}

	public function actionBookings()
	{
		$result = [];
		$patient = $this->orm->get('Patient');

		if ($q = $this->request->get('query')) {
			$patient->where($this->pixie->db->expr("CONCAT_WS(' ',first_name,last_name)"), 'like', '%' . $q . '%');
		}
		$patient->where('organization_id', $this->org->id)
			->where('status', Patient::STATUS_ACTIVE)
			->order_by('first_name', 'asc')->order_by('last_name', 'asc')
			->limit(12);

		foreach ($patient->find_all() as $patient) {
			$result[] = $patient->toShortArray();
		}
		$this->result = $result;
	}

	public function actionCompileBookings()
	{
		try {

			$bookings = $this->request->post('bookings');
			$user = $this->logged();

			if (!$bookings || !is_array($bookings)) {
				throw new \Exception('Bookings list is empty');
			}

			$documentsToPrint = [];
			$bookingIdsToPrint = [];
			foreach ($bookings as $bookingId) {
				/** @var \Opake\Model\Booking $bookingModel */
				$bookingModel = $this->orm->get('Booking', $bookingId);
				if ($bookingModel->loaded()) {
					if ($user->isInternal() || $user->organization_id == $bookingModel->organization_id) {
						$bookingIdsToPrint[] = $bookingId;
						$documentsToPrint[] = new \OpakeAdmin\Helper\Printing\Document\Booking\Booking($bookingModel);
					}
				}
			}

			if (!$documentsToPrint) {
				throw new \Exception('Document for print list is empty');
			}

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$result = $helper->compile($documentsToPrint);

			$this->pixie->activityLogger
				->newAction(ActivityRecord::ACTION_BOOKING_PRINT)
				->setArray($bookingIdsToPrint)
				->register();

			$this->result = [
				'success' => true,
				'id' => $result->id(),
				'url' => $result->getResultUrl()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionExportBooking()
	{
		try {
			$data = $this->getData();

			if (!$data) {
				throw new BadRequest('Bad Request');
			}

			$document = new \OpakeAdmin\Helper\Printing\Document\Booking\BookingForm($data);
			$printHelper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$result = $printHelper->compile([$document]);

			$action = $this->pixie->activityLogger
				->newAction(ActivityRecord::ACTION_BOOKING_PRINT);

			if (isset($data->id)) {
				$action->setArray([$data->id]);
			}

			$action->register();

			$this->result = [
				'success' => true,
				'id' => $result->id(),
				'url' => $result->getResultUrl()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionPatientInsurances()
	{
		$model = $this->loadModel('Booking_Patient', 'subid');
		$user = $this->logged();

		if (!$user || (!$user->isInternal() && $user->organization_id != $model->organization_id)) {
			throw new Forbidden();
		}

		$insurances = $model->insurances->find_all();

		$result = [];

		foreach ($insurances as $insurance) {
			$result[] = $insurance->toArray();
		}

		$this->result = [
			'success' => true,
			'insurances' => $result
		];
	}

	protected function updateBookingInsurances($booking, $data, $patientModel, $isModelCreate = false)
	{
		$updater = new \OpakeAdmin\Helper\Insurance\InputDataUpdater\BookingInsuranceUpdater(
			$booking, $data, $patientModel, $isModelCreate
		);

		$updater->update();
	}

	protected function updateMultipleFields($model, $data) {
		if(isset($data->pre_op_required_data)) {
			$model->updatePreOpRequiredData($data->pre_op_required_data);
		}
		if(isset($data->studies_ordered)) {
			$model->updateStudiesOrdered($data->studies_ordered);
		}
	}

	protected function createBookingSheetSnapshot($bookingModel, $bookingData)
	{
		$document = new \OpakeAdmin\Helper\Printing\Document\Booking\BookingForm($bookingData);
		$document->runCompile();

		$app = \Opake\Application::get();
		/** @var \Opake\Model\UploadedFile $uploadedFile */
		$uploadedFile = $app->orm->get('UploadedFile');
		$uploadedFile->storeContent($document->getFileName(), $document->getContent(), [
			'is_protected' => true,
			'is_assigned' => true,
			'mime_type' => $document->getContentMimeType()
		]);
		$uploadedFile->save();

		$chart = $this->orm->get('Cases_Chart');
		$chart->list_id = $bookingModel->getCaseBookingListId();
		$chart->name = 'Booking Sheet';
		$chart->uploaded_file_id = $uploadedFile->id();
		$chart->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
		$chart->is_booking_sheet = true;
		$chart->save();
	}

	public function actionGetUnscheduledCount()
	{
		$model = $this->orm->get('Booking')
			->where('organization_id', $this->org->id)
			->where('status', '!=', Booking::STATUS_SCHEDULED);

		$query = $model->query;
		$query->fields('booking_sheet.*');

		$query->where('and', [
			[
				[$model->table . '.is_updated_by_satellite', 1],
				[$model->table . '.status', \Opake\Model\Booking::STATUS_SUBMITTED]
			],
			[
				'or', [
				[$model->table . '.is_updated_by_satellite', 0],
			]
			]
		]);

		$results = $model->find_all()->as_array();
		$count = 0;
		foreach ($results as $result) {
			if ($result->isValidForSchedule()) {
				$count++;
			}
		}

		$this->result = $count;
	}

	public function actionGetExistingPatients()
	{
		$data = $this->getData();

		$patientsWithSimilarFirstAndLastName = $this->orm->get('Patient')
			->where('first_name', $data->first_name)
			->where('and', ['last_name', $data->last_name])
			->where('and', ['status', '!=', Patient::STATUS_ARCHIVE])
			->where('and', ['organization_id', $this->org->id]);

		$patientsWithSimilarFirstNameAndDob = $this->orm->get('Patient')
			->where('first_name', $data->first_name)
			->where('and', ['dob', \Opake\Helper\TimeFormat::formatToDB($data->dob)])
			->where('and', ['status', '!=', Patient::STATUS_ARCHIVE])
			->where('and', ['organization_id', $this->org->id]);

		$patientsWithSimilarLastNameAndDob = $this->orm->get('Patient')
			->where('last_name', $data->last_name)
			->where('and', ['dob', \Opake\Helper\TimeFormat::formatToDB($data->dob)])
			->where('and', ['status', '!=', Patient::STATUS_ARCHIVE])
			->where('and', ['organization_id', $this->org->id]);

		$patients = array_merge($patientsWithSimilarFirstAndLastName->find_all()->as_array(),
			$patientsWithSimilarFirstNameAndDob->find_all()->as_array(),
			$patientsWithSimilarLastNameAndDob->find_all()->as_array());

		$patientsArray = [];
		$patientsIds = [];
		foreach ($patients as $patient) {
			if (!in_array($patient->id, $patientsIds)) {
				$patientsArray[] = $patient->toBookingExistingArray();
				$patientsIds[] = $patient->id;
			}
		}

		$this->result = $patientsArray;
	}

	public function actionGetNewBookingInfo()
	{
		$user = $this->logged();
		$availableTemplates = BookingSheetTemplate::getAvailableTemplatesForUser($user);
		$data = [];
		foreach ($availableTemplates as $template) {
			$data[] = $template->getFormatter('TemplateBookingSheet')
				->toArray();
		}

		$this->result = [
			'success' => true,
		    'templates' => $data,
		    'display_point_of_contact' => (bool) $this->org->sms_template->poc_sms
		];
	}

	/**
	 * @param \Opake\Model\Booking $booking
	 * @return ModelActionQueue
	 */
	protected function createBookingActionQueue($booking)
	{
		/** @var \Opake\ActivityLogger $logger */
		$logger = $this->pixie->activityLogger;

		$queue = $logger->newModelActionQueue($booking);

		if (!$booking->loaded()) {
			$queue->addAction(ActivityRecord::ACTION_BOOKING_CREATE);
		} else {
			$queue->addAction(ActivityRecord::ACTION_BOOKING_EDIT);
		}

		$queue->assign();

		return $queue;
	}

	protected function saveBookingTemplateSnapshot($booking, $data)
	{
		if (!empty($data->template)) {
			if (isset($data->template->fields)) {
				$fields = $data->template->fields;
				$existedSnapshotCount = $this->orm->get('BookingSheetTemplate_Snapshot')
					->where('booking_id', $booking->id())
					->count_all();
				if ($existedSnapshotCount > 0) {
					throw new \Exception('Shapshot of template for this booking sheet already exists');
				}

				$snapshotModel = $this->orm->get('BookingSheetTemplate_Snapshot');
				$snapshotModel->booking_id = $booking->id();
				$snapshotModel->original_template_id = (isset($data->template->id)) ? $data->template->id : null;
				$snapshotModel->save();

				foreach ($fields as $fieldId => $fieldData) {
					$fieldModel = $this->orm->get('BookingSheetTemplate_Snapshot_Field');
					$fieldModel->booking_sheet_template_snapshot_id = $snapshotModel->id();
					$fieldModel->field = $fieldId;
					$fieldModel->x = $fieldData->x;
					$fieldModel->y = $fieldData->y;
					$fieldModel->save();
				}
			}
		}
	}
}
