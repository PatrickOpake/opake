<?php

namespace OpakeAdmin\Controller\Patients;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\InvalidMethod;
use Opake\Exception\PageNotFound;
use Opake\Extentions\Mail\SMTP;
use Opake\Helper\Config;
use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Patient;
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

		$model = $this->orm->get('Patient')->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Patient($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionPatient()
	{
		$model = $this->loadModel('Patient', 'subid');
		$user = $this->logged();

		if (!$user || (!$user->isInternal() && $user->organization_id != $model->organization_id)) {
			throw new Forbidden();
		}

		$this->result = $model->toArray();
	}

	public function actionPatientInsurances()
	{
		$model = $this->loadModel('Patient', 'subid');
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

	public function actionValidateInsurance()
	{
		$data = $this->getData(true);
		$model = $this->pixie->orm->get('Patient_Insurance');

		$form = new \OpakeAdmin\Form\Insurance\InsuranceEditForm($this->pixie, $model);
		$form->load($data);

		if (!$form->isValid()) {
			$this->result = [
				'success' => false,
				'errors' => $form->getCommonErrorList()
			];
			return;
		}

		if (isset($data['data'])) {
			$dataModelForm = $form->getDataModelForm();
			$dataModelForm->load($data['data']);

			if (!$dataModelForm->isValid()) {
				$this->result = [
					'success' => false,
					'errors' => $dataModelForm->getCommonErrorList()
				];
				return;
			}
		}

		$this->result = [
			'success' => true
		];

	}

	public function actionOperativeReports()
	{
		$model = $this->loadModel('Patient', 'subid');
		$user = $this->logged();

		if (!$user || (!$user->isInternal() && $user->organization_id != $model->organization_id)) {
			throw new Forbidden();
		}

		$search = new \OpakeAdmin\Model\Search\Cases\OperativeReport($this->pixie);
		$reports = $search->searchForPatient($this->orm->get('Cases_OperativeReport'), $model->id);

		$result = [];
		foreach ($reports as $report) {
			$result[] = $report->toShortArray();
		}

		$this->result = [
			'success' => true,
			'reports' => $result
		];
	}

	public function actionCharts()
	{
		$patient = $this->loadModel('Patient', 'subid');
		$patientCharts = []; ;
		foreach ($patient->charts->find_all() as $chart) {
			$patientCharts[] = $chart->toArray();
		}

		$casesArray = [];
		$cases = $this->orm->get('Cases_Item');
		$caseQuery = $cases->query;
		$caseQuery->fields('case.*');

		$caseQuery->where('organization_id', $this->org->id)
			->join('case_registration', ['case_registration.case_id', 'case.id'], 'inner')
			->where('case_registration.patient_id', $patient->id);

		foreach ($cases->find_all() as $case) {
			$casesArray[] = $case->toChartsArray();
		}

		$this->result = [
			'patientCharts' => $patientCharts,
			'cases' => $casesArray,
		];
	}

	public function actionFinancialDocuments()
	{
		$patient = $this->loadModel('Patient', 'subid');
		$financialDocuments = []; ;
		foreach ($patient->financial_documents->find_all() as $doc) {
			$financialDocuments[] = $doc->toArray();
		}

		$casesArray = [];
		$cases = $this->orm->get('Cases_Item');
		$caseQuery = $cases->query;
		$caseQuery->fields('case.*');

		$caseQuery->where('organization_id', $this->org->id)
			->join('case_registration', ['case_registration.case_id', 'case.id'], 'inner')
			->where('case_registration.patient_id', $patient->id);

		foreach ($cases->find_all() as $case) {
			$casesArray[] = $case->getFormatter('FinancialDocsFormatter')->toArray();
		}

		$this->result = [
			'financialDocuments' => $financialDocuments,
			'cases' => $casesArray,
		];
	}

	public function actionUploadDoc()
	{
		try {
			$docType = $this->request->post('doc_type');
			$patient = $this->loadModel('Patient', 'subid');

			if ($this->request->method !== 'POST') {
				throw new InvalidMethod('Invalid method');
			}

			/** @var \Opake\Request $req */
			$req = $this->request;

			$files = $req->getFiles();
			if (empty($files['file'])) {
				throw new BadRequest('Empty file');
			}

			$upload = $files['file'];
			if (!$upload->isEmpty() && !$upload->hasErrors()) {
				$fileProtectedType = 'patient_chart';
				if($docType == 'financial_document') {
					$fileProtectedType = 'patient_financial_document';
				}
				/** @var \Opake\Model\UploadedFile $uploadedFile */
				$uploadedFile = $this->pixie->orm->get('UploadedFile');
				$uploadedFile->storeFile($upload, [
					'is_protected' => true,
					'protected_type' => $fileProtectedType
				]);
				$uploadedFile->save();
				$docId = $this->request->post('doc_id');
				if($docType === 'financial_document') {
					if ($docId) {
						$doc = $this->orm->get('Patient_FinancialDocument', $docId);
					} else {
						$doc = $this->orm->get('Patient_FinancialDocument');
						$doc->patient_id = $patient->id;
					}
				} else {
					if ($docId) {
						$doc = $this->orm->get('Patient_Chart', $docId);
					} else {
						$doc = $this->orm->get('Patient_Chart');
						$doc->patient_id = $patient->id;
					}
				}

				$doc->name = $this->request->post('doc_name');
				$doc->uploaded_file_id = $uploadedFile->id;
				$doc->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
				$doc->save();
			}

			$this->result = 'ok';

		} catch (\Exception $e) {
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionRemoveDoc()
	{
		$docType = $this->request->post('doc_type');
		if($docType === 'financial_document') {
			$doc = $this->loadModel('Patient_FinancialDocument', 'subid');
		} else {
			$doc = $this->loadModel('Patient_Chart', 'subid');
		}
		if ($doc->loaded()) {
			$doc->delete();
			$this->result = 'ok';
		}
	}

	public function actionCompileDocs()
	{
		try {

			$documents = $this->request->post('documents');
			$docType = $this->request->post('doc_type');

			if (!$documents || !is_array($documents)) {
				throw new \Exception('Documents list is empty');
			}

			$documentsList = [];
			foreach ($documents as $document) {
				if (isset($document['type']) && ($document['type'] === 'report')) {
					$reportId = $document['id'];
					$report = $this->pixie->orm->get('Cases_OperativeReport', $reportId);
					if ($report->loaded()) {
						$documentsList[] = new \OpakeAdmin\Helper\Printing\Document\Cases\OperativeReport($report);
					}
				} else {
					if($docType === 'financial_document') {
						if (isset($document['patient_id'])) {
							$documentObject = $this->pixie->orm->get('Patient_FinancialDocument', $document['id']);
						} else {
							$documentObject = $this->pixie->orm->get('Cases_FinancialDocument', $document['id']);
						}
					} else {
						if (isset($document['patient_id'])) {
							$documentObject = $this->pixie->orm->get('Patient_Chart', $document['id']);
						} else {
							$documentObject = $this->pixie->orm->get('Cases_Chart', $document['id']);
						}
					}


					$documentsList[] = new \OpakeAdmin\Helper\Printing\Document\Cases\AdditionalChart\ChartFile($documentObject);
				}
			}

			$helper =  new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$result = $helper->compile($documentsList);

			$this->result = [
				'success' => true,
				'id' => $result->id(),
				'url' => $result->getResultUrl(),
				'print' => $result->isReadyToPrint()
			];

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

	public function actionGenerateMrn()
	{
		/** @var \Opake\Model\Patient $newPatient */
		$newPatient = $this->pixie->orm->get('Patient');
		$newPatient->initMrn($this->org->id());

		$this->result = [
			'success' => true,
			'mrn' => $newPatient->getFormattedMrn(),
			'mrn_year' => $newPatient->getFormattedMrnYear()
		];
	}

	public function actionPortalEmailText()
	{
		$this->result = [
			'success' => true,
			'text' => 'mailto'
		];
	}

	public function actionSave()
	{
		$service = $this->services->get('patients');
		$formType = $this->request->post('form');
		$data = $this->getData();

		$savePatientDetails = (!$formType || $formType === 'patient_details');
		$saveInsurances = (!$formType || $formType === 'insurances');

		if (!$data) {
			throw new BadRequest('Bad Request');
		}

		$model = $this->orm->get('Patient', isset($data->id) ? $data->id : null);

		if (!$model->loaded()) {
			$model->organization_id = $this->org->id;
			$data->organization_id = $this->org->id;
		} elseif ($model->organization_id !== $this->org->id) {
			throw new \Opake\Exception\Ajax('Patient doesn\'t exist');
		}

		$model->beginTransaction();
		try {

			$isModelCreate = (!$model->loaded());

			if ($savePatientDetails) {
				$model->fill($data);

				$form = new PatientMrnForm($this->pixie, $model);
				$form->load(json_decode($this->request->post('data', null, false), true));
				if (!$form->isValid()) {
					throw new \Opake\Exception\ValidationError($form->getFirstErrorMessage());
				}

				$queue = $this->pixie->activityLogger->newModelActionQueue($model);
				if (!$model->loaded()) {
					$queue->addAction(ActivityRecord::ACTION_PATIENT_CREATE);
				} else {
					$queue->addAction(ActivityRecord::ACTION_PATIENT_EDIT);
				}
				$queue->assign();

				$this->checkValidationErrors($model, $model->getNameAndDobValidator());

				$model->save();

				$queue->registerActions();
			}

			if ($saveInsurances) {
				$this->updatePatientInsurances($model, $data, $isModelCreate);
			}

			if ($savePatientDetails || $saveInsurances) {
				$service->updateExistedRegistrations($model);
			}

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$model->rollback();
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
			return;
		}
		$model->commit();

		$this->result = [
			'success' => true,
			'id' => (int)$model->id
		];
	}

	public function actionRemovePatient() {
		$service = $this->services->get('patients');
		$withCase = (int)$this->request->post('withCase');
		$model = $this->loadModel('Patient', 'subid');

		if ($withCase) {
			$service->deleteWithCases($model);
		} else {
			$service->deleteOnlyWithBookings($model);
		}
	}

	public function actionArchivePatient() {
		$model = $this->loadModel('Patient', 'subid');
		$status = (int)$this->request->post('status');
		$model->status  = $status;
		$model->save();
	}

	/**
	 * @param AbstractModel $model
	 * @param \PHPixie\Validate\Validator $validator
	 * @throws \Exception
	 */
	protected function checkValidationErrors($model, $validator = null, $errorIsObject = false, $key = null, $model_name=null)
	{
		if (!$validator) {
			$validator = $model->getValidator();
		}

		if (!$validator->valid()) {
			if ($errorIsObject) {
				if (!is_null($key)) {
					$errors[$model_name][$key] = $validator->errors();
				} else {
					$errors[$model_name] = $validator->errors();
				}
				$errors['length'] = count($validator->errors());
				$error = json_encode($errors);
			} else {
				$errors_text = '';
				foreach ($validator->errors() as $field => $errors) {
					$errors_text .= implode('; ', $errors) . '; ';
				}
				$error = trim($errors_text, '; ');
			}

			throw new \Opake\Exception\ValidationError($error);
		}
	}

	public function actionValidate()
	{
		$data = $this->getData();
		$service = $this->services->get('patients');
		if (!isset($data->organization_id)) {
			$data->organization_id = $this->org->id();
		}
		$this->result = [
			'errors' => json_encode($service->validate('Patient', 'Patient_Insurance', $data))
		];
	}

	public function actionPatients()
	{
		$result = [];
		$patient = $this->orm->get('Patient');

		if ($q = $this->request->get('query')) {
			$patient->where('and', [
				['or', [$this->pixie->db->expr("CONCAT_WS(' ',first_name,last_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(' ',last_name,first_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(', ',first_name, last_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(', ',last_name,first_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(',',last_name,first_name)"), 'like', '%' . $q . '%']],
				['or', [$this->pixie->db->expr("CONCAT_WS(',',first_name,last_name)"), 'like', '%' . $q . '%']]
			]);
		}
		$patient->where('organization_id', $this->org->id)
			->where('status', Patient::STATUS_ACTIVE)
			->order_by('first_name', 'asc')->order_by('last_name', 'asc')
			->limit(12);

		$search = new \OpakeAdmin\Model\Search\Patient($this->pixie, false);
		$results = $search->search($patient, $this->request);

		foreach ($results as $patient) {
			$result[] = $patient->toShortArray();
		}
		$this->result = $result;
	}

	public function actionUserPatients()
	{
		$result = [];
		$patient = $this->orm->get('Patient');
		$patientQuery = $patient->query;
		$patientQuery->fields('patient.*');

		if ($q = $this->request->get('query')) {
			$patientQuery->where($this->pixie->db->expr("CONCAT_WS(' ',first_name,last_name)"), 'like', '%' . $q . '%');
		}

		$patientQuery->where('organization_id', $this->org->id)
			->join('case_registration', ['case_registration.patient_id', 'patient.id'], 'inner')
			->join('case_user', ['case_user.case_id', 'case_registration.case_id'], 'inner')
			->where('case_user.user_id', $this->logged()->id())
			->order_by('first_name', 'asc')
			->order_by('last_name', 'asc')
			->group_by('patient.id')
			->limit(12);

		foreach ($patient->find_all() as $patient) {
			$result[] = $patient->toShortArray();
		}

		$this->result = $result;
	}

	protected function updatePatientInsurances($patient, $data, $isModelCreate = false)
	{
		$updater = new \OpakeAdmin\Helper\Insurance\InputDataUpdater\PatientInsuranceUpdater(
			$patient, $data, $isModelCreate
		);

		$updater->update();
	}

	public function actionPreparePatientLoginMail()
	{
		$this->checkAccess('patient-portal', 'send_login_email');

		$patient = $this->loadPatientToSendMail();

		$currentDate = new \DateTime();

		$portalUser = $this->pixie->orm->get('Patient_User');
		$portalUser->where('patient_id', $patient->id());
		$portalUser = $portalUser->find();

		if (!$portalUser->loaded()) {
			$portalUser = $this->pixie->orm->get('Patient_User');
			$portalUser->patient_id = $patient->id();
			$portalUser->created = TimeFormat::formatToDBDatetime($currentDate);
			$portalUser->active = 1;
		} else {
			$portalUser->active = 1;
		}

		$randomPassword = $portalUser->generateRandomPassword();
		$portalUser->new_gen_password = $randomPassword;
		$portalUser->save();

		$this->result = [
			'success' => true,
			'mail' => $this->getPatientLoginMail($patient, $randomPassword)
		];
	}

	public function actionSendPatientLoginMail()
	{
		$this->checkAccess('patient-portal', 'send_login_email');

		$patient = $this->loadPatientToSendMail();

		$body = $this->request->post('body');
		$subject = $this->request->post('subject');

		$to = sprintf("<%s> %s", $patient->getEmail(), $patient->getFullName());
		$from = $patient->organization->contact_email;

		$smtp = new SMTP();
		$smtp->setConfig(Config::get('mail.accounts.default'));
		$smtp->setFrom($from);
		$smtp->send($to, $subject, $body);

		$portalUser = $this->pixie->orm->get('Patient_User');
		$portalUser->where('patient_id', $patient->id());
		$portalUser = $portalUser->find();

		if (!$portalUser->loaded()) {
			throw new \Exception('Portal user is not loaded');
		}

		if ($portalUser->new_gen_password) {
			$portalUser->setPassword($portalUser->new_gen_password);
			$portalUser->new_gen_password = null;
			$portalUser->is_tmp_password = 1;
			$portalUser->save();
		}

		$this->result = [
			'success' => true
		];

	}

	public function actionCancelPatientLoginMail()
	{
		$patient = $this->loadPatientToSendMail();
		$portalUser = $this->pixie->orm->get('Patient_User');
		$portalUser->where('patient_id', $patient->id());
		$portalUser = $portalUser->find();

		if (!$portalUser->loaded()) {
			throw new \Exception('Portal user is not loaded');
		}

		if ($portalUser->new_gen_password) {
			$portalUser->new_gen_password = null;
			$portalUser->save();
		}
	}

	public function actionHasDuplicatePatient()
	{
		$patientId = $this->request->param('subid');
		$patient = $this->pixie->orm->get('Booking_Patient', $patientId);

		if ($patient->loaded()) {
			$patientsWithSimilarFirstAndLastName = $this->orm->get('Patient')
				->where('first_name', $patient->first_name)
				->where('and', ['last_name', $patient->last_name]);

			$patientsWithSimilarFirstNameAndDob = $this->orm->get('Patient')
				->where('first_name', $patient->first_name)
				->where('and', ['dob', \Opake\Helper\TimeFormat::formatToDB($patient->dob)]);

			$patientsWithSimilarLastNameAndDob = $this->orm->get('Patient')
				->where('last_name', $patient->last_name)
				->where('and', ['dob', \Opake\Helper\TimeFormat::formatToDB($patient->dob)]);

			if ($patientsWithSimilarFirstAndLastName->count_all()
				|| $patientsWithSimilarFirstNameAndDob->count_all()
				|| $patientsWithSimilarLastNameAndDob->count_all()
			) {
				$this->result = true;
			} else {
				$this->result = false;
			}
		} else {
			$this->result = false;
		}
	}

	public function actionHasSamePatientExists()
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

		if ($patientsWithSimilarFirstAndLastName->count_all()
			|| $patientsWithSimilarFirstNameAndDob->count_all()
			|| $patientsWithSimilarLastNameAndDob->count_all()
		) {
			$this->result = ['patient_exists' => true];
		} else {
			$this->result = ['patient_exists' => false];
		}
	}

	protected function loadPatientToSendMail()
	{
		$patientId = $this->request->param('subid');
		if (!$patientId) {
			throw new BadRequest('Patient is not specified');
		}

		$patient = $this->pixie->orm->get('Patient', $patientId);

		if (!$patient->loaded()) {
			throw new PageNotFound('Unknown patient');
		}

		if (!$patient->organization->portal || !$patient->organization->portal->loaded()) {
			throw new \Exception('Portal is not existed');
		}

		return $patient;
	}

	protected function getPatientLoginMail($patient, $password)
	{
		$to = sprintf("<%s> %s", $patient->getEmail(), $patient->getFullName());
		$portalName = $patient->organization->portal->title;
		$portalUrl = $patient->organization->portal->getFullUrl();
		$from = $patient->organization->contact_email;

		$subject = $portalName . " Patient Portal Login information";

		$template = "Here are your credentials to login to the %portal_name% Opake patient portal.\n" .
			"To login: \n\n" .
			"1. Visit the url: %portal_url% \n" .
			"2. Enter the following credentials:\n\n" .
			"Username: %email%\n" .
			"Password: %password%\n\n" .
			"3. Create a new password\n" .
			"4. Login and fill out your information\n\n" .
			"You can login at any time to view details about your upcoming visit, access your documentation, and to fill out additional required information.";

		$placeholders = array(
			'%portal_name%' => $portalName,
			'%portal_url%' => $portalUrl,
			'%email%' => $patient->home_email,
			'%password%' => $password
		);
		$body = strtr($template, $placeholders);

		return [
			'to' => $to,
			'from' => $from,
			'subject' => $subject,
			'body' => $body
		];

	}

}
