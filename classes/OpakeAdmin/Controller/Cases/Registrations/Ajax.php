<?php

namespace OpakeAdmin\Controller\Cases\Registrations;

use Opake\Exception\BadRequest;
use Opake\Exception\InvalidMethod;
use Opake\Helper\TimeFormat;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Cases\Registration;
use OpakeAdmin\Form\Cases\AdditionalChartUploadForm;

class Ajax extends \OpakeAdmin\Controller\Ajax {

	protected $docPath = '/uploads/cases/registrations/';

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$this->checkAccess('cases', 'view');

		$items = [];

		$model = $this->orm->get('Cases_Registration')->where('organization_id', $this->org->id);

		$subid = $this->request->param('subid');
		if ($subid) {
			$model->where('patient_id', $subid);
		}

		$search = new \OpakeAdmin\Model\Search\Cases\Registration($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
		    'items' => $items,
		    'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionRegistration()
	{
		$model = $this->loadModel('Cases_Registration', 'subid');
		$this->result = $model->toArray();
	}

	public function actionCompleteRegistration()
	{
		$model = $this->loadModel('Cases_Registration', 'subid');
		$model->complete();
	}

	public function actionReopenRegistration()
	{
		$model = $this->loadModel('Cases_Registration', 'subid');
		$model->reopen();

		$this->result = $model->status;
	}

	public function actionSave()
	{
		$data = $this->getData();
		$saveType = $this->request->post('type');

		$service_patient = $this->services->get('patients');

		/** @var \Opake\Model\Cases\Registration $model */
		$model = $this->orm->get('Cases_Registration', isset($data->id) ? $data->id : null);

		if ($model->loaded()) {
			$this->checkAccess('cases', 'edit', $model);
		}

		$isModelCreate = (!$model->loaded());

		$this->updateInsurancesForRegistration($model, $data, $isModelCreate);

		$model->beginTransaction();
		try {
			if (!$model->loaded()) {
				$model->organization_id = $this->org->id;
				$data->organization_id = $this->org->id;
			} elseif ($model->organization_id !== $this->org->id) {
				throw new \Opake\Exception\Ajax('Case registration doesn\'t exist');
			}

			if ($data) {
				$model->fill($data);
			}

			if ($model->isAllSectionsValid()) {
				$model->status = Registration::STATUS_SUBMIT;
			} else {
				$model->status = Registration::STATUS_UPDATE;
			}

			$actionQueue = null;
			if ($model->loaded()) {
				$actionQueue = $this->pixie->activityLogger->newModelActionQueue($model);
				$actionQueue->addAction(ActivityRecord::ACTION_INTAKE_EDIT_PATIENT_DETAILS);
				$actionQueue->assign();
			}

			$model->save();

			$model->case->updateStagePhase();

			// update patient info from registration
			$patient = $this->pixie->orm->get('Patient', isset($model->patient_id) ? $model->patient_id : null);
			$patient->fromRegistration($model);

			$patient->save();
			$service_patient->updateExistedRegistrations($patient);

			if ($actionQueue) {
				$actionQueue->registerActions();
			}

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$model->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$model->commit();
		$this->result = [
			'id' => (int)$model->id,
			'status' => (int)$model->status
		];
	}

	public function actionValidate()
	{
		$data = $this->getData();
		$service = $this->services->get('patients');
		$this->result = [
			'errors' => json_encode($service->validate('Cases_Registration', 'Cases_Registration_Insurance', $data))
		];
	}

	public function actionUpload()
	{

		try {

			$case = $this->loadModel('Cases_Item', 'subid');

			if ($case->registration->loaded()) {
				$registration = $case->registration;
			} else {
				$registration = $this->orm->get('Cases_Registration');
				$registration->case_id = $case->id;
			}

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$postData = $req->post();
			$postData['files'] = $req->getFiles();

			$form = new AdditionalChartUploadForm($this->pixie);
			$form->setIsNewDocument(false);
			$form->load($postData);
			if (!$form->isValid()) {
				$this->result = [
					'success' => false,
					'error' => $form->getFirstErrorMessage()
				];
				return;
			}

			$files = $req->getFiles();
			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}

			$upload = $files['file'];
			$uploadType = $this->request->post('document_type');

			if (!$upload->isEmpty() && !$upload->hasErrors()) {
				/** @var \Opake\Model\UploadedFile $uploadedFile */
				$uploadedFile = $this->pixie->orm->get('UploadedFile');
				$uploadedFile->storeFile($upload, [
					'is_protected' => true,
					'protected_type' => 'cases_registration'
				]);
				$uploadedFile->save();

				$oldDocument = $registration->documents->where('document_type', $uploadType)->find();
				if (!$oldDocument->loaded()) {
					$oldDocument = null;
				}

				$newDocument = $registration->addDocument($uploadType, $uploadedFile);

				$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_INTAKE_EDIT_FORMS)
					->setNewAndOldModels($newDocument, $oldDocument)
					->register();

			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}

	}

	public function actionUploadNewType()
	{

		try {

			$case = $this->loadModel('Cases_Item', 'subid');

			if ($case->registration->loaded()) {
				$registration = $case->registration;
			} else {
				$registration = $this->orm->get('Cases_Registration');
				$registration->case_id = $case->id;
			}

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$postData = $req->post();
			$postData['files'] = $req->getFiles();

			$form = new AdditionalChartUploadForm($this->pixie);
			$form->setIsNewDocument(true);
			$form->load($postData);
			if (!$form->isValid()) {
				$this->result = [
					'success' => false,
				    'error' => $form->getFirstErrorMessage()
				];
				return;
			}

			$files = $req->getFiles();
			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}

			$upload = $files['file'];
			$uploadType = $this->request->post('name');

			if (!$upload->isEmpty() && !$upload->hasErrors()) {

				/** @var \Opake\Model\UploadedFile $uploadedFile */
				$uploadedFile = $this->pixie->orm->get('UploadedFile');
				$uploadedFile->storeFile($upload, [
					'is_protected' => true,
					'protected_type' => 'cases_registration'
				]);
				$uploadedFile->save();

				$typeModel = $this->orm->get('Cases_Registration_Document_Type');
				$typeModel->organization_id = $this->org->id;
				$typeModel->name = $uploadType;
				$typeModel->is_required = false;
				$typeModel->save();

				$document = $registration->addDocument($typeModel->id(), $uploadedFile);

				$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_INTAKE_EDIT_FORMS)
					->setModel($document)
					->register();

			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionRemoveFile()
	{
		$case = $this->loadModel('Cases_Item', 'subid');
		$docid = $this->request->post('docid');
		if($case->registration->loaded()) {
			$registration = $case->registration;

			if ($registration->organization_id !== $this->org->id) {
				\Opake\Exception\Ajax('Document doesn\'t exist');
			}

			$registration->removeDocument($docid);

			$this->result = 'ok';
		}
	}

	public function actionChangeStatus()
	{
		$data = $this->getData();
		$case = $this->loadModel('Cases_Item', 'subid');
		$model = $this->pixie->orm->get('Cases_Registration_Document', isset($data->id) ? $data->id : null);

		$registration = $case->registration;
		if ($registration->loaded()) {
			$model->document_type = $data->type;
			$model->case_registration_id = $registration->id;
			$model->status = $data->status;

			$queue = $this->pixie->activityLogger->newModelActionQueue($model);
			$queue->addAction(ActivityRecord::ACTION_INTAKE_EDIT_FORMS);
			$queue->assign();

			$model->save();

			if ($registration->isAllSectionsValid()) {
				$registration->status = Registration::STATUS_SUBMIT;
			} else {
				$registration->status = Registration::STATUS_UPDATE;
			}

			$registration->save();
			$queue->registerActions();

			$this->result = 'ok';
		} else {
			throw new \Opake\Exception\Ajax('Case doesn\'t exist');
		}

	}

	public function actionExistedDocs()
	{
		$service = $this->services->get('cases');
		$doc_type = $this->request->get('doc_type');
		$case = $this->loadModel('Cases_Item', 'subid');

		$documents = $service->getExistedDocs($case, $doc_type);
		$result = [];
		foreach($documents as $doc) {
			$result[] = $doc->toArray();
		}
		$this->result = $result;
	}

	public function actionCopyExistedDoc()
	{
		$case = $this->loadModel('Cases_Item', 'subid');

		$from_id = $this->request->post('from');
		$to_id = $this->request->post('to');

		$from_model = $this->pixie->orm->get('Cases_Registration_Document', $from_id);
		$to_model = $this->pixie->orm->get('Cases_Registration_Document', isset($to_id) ? $to_id : null);

		if($from_model->loaded()) {
			$new_file = $this->pixie->orm->get('UploadedFile');
			try {
				$new_uploaded_file  = $new_file->storeLocalFile( $from_model->file->getSystemPath(), [
					'is_protected' => 1,
					'is_assigned' => 0,
					'protected_type' => 'cases_registration'
				]);
				$new_uploaded_file->save();
			} catch (\Exception $e) {
				throw new \Opake\Exception\Ajax('Can\'t copy document file');
			}

			$to_model->case_registration_id = $case->registration->id();
			$to_model->document_type = $from_model->document_type;
			$to_model->uploaded_file_id = $new_uploaded_file->id();
			$to_model->uploaded_date = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB);
			$to_model->status = 1;
			$to_model->save();
			$this->result = 'ok';
		} else {
			throw new \Opake\Exception\Ajax('Document doesn\'t exist');
		}
	}

	protected function updateInsurancesForRegistration($model, $data, $isModelCreate = false)
	{

		$model->beginTransaction();
		try {

			$updater = new \OpakeAdmin\Helper\Insurance\InputDataUpdater\CaseInsuranceUpdater(
				$model, $data, $isModelCreate
			);

			$updater->update();

			$model->commit();
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$model->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

	}

}
