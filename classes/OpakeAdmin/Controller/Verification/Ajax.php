<?php

namespace OpakeAdmin\Controller\Verification;

use Opake\Exception\ValidationError;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Cases\Item;
use Opake\Model\Cases\Registration;
use \Opake\Model\Cases\Registration\Insurance\Verification;
use OpakeAdmin\Form\Insurance\VerificationForm;

class Ajax extends \OpakeAdmin\Controller\Ajax {

	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$service = $this->services->get('Cases');

		$model = $service->getItem()
			->where('organization_id', $this->org->id())
			->where('stage', '!=', Item::STAGE_BILLING)
			->where('appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED);

		$search = new \OpakeAdmin\Model\Search\Cases($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->getFormatter('VerificationList')->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionSave()
	{
		$data = $this->getData();

		$caseRegistrationId = $data->caseRegistrationId ?? null;
		$caseInsuranceId = $data->caseInsuranceId ?? null;

		/** @var \Opake\Model\Cases\Registration $registration */
		$registration = $this->orm->get('Cases_Registration', $caseRegistrationId);
		if (!$registration->loaded() || $registration->organization_id !== $this->org->id) {
			throw new \Opake\Exception\Ajax('Case registration doesn\'t exist');
		}
		$this->checkAccess('cases', 'edit', $registration);

		$verificationId = isset($data->verification) && isset($data->verification->id) ? $data->verification->id : null;
		/** @var Verification $verification */
		$verification = $this->orm->get('Cases_Registration_Insurance_Verification', $verificationId);

		$verification->beginTransaction();

		try {
			$form = new VerificationForm($this->pixie, $verification);
			$form->load($data->verification);

			if (!$form->isValid()) {
				throw new ValidationError(implode("\n", $form->getCommonErrorList()));
			}

			$form->fillModel();
			if (!$verification->loaded()) {
				$verification->case_registration_id = $caseRegistrationId;
				$verification->case_insurance_id = $caseInsuranceId;
			}

			$verification->updateVerificationStatus();

			$queueVerification = $this->pixie->activityLogger->newModelActionQueue($verification);
			$queueVerification->addAction(ActivityRecord::ACTION_CLINICAL_VERIFICATION_EDIT);
			$queueVerification->assign();

			$verification->save();

			$queueVerification->registerActions();

			$verification->case_types->delete_all();

			$cpts = $form->getCpts();
			foreach ($cpts as $cpt) {
				$cptModel = $this->orm->get('Cases_Registration_Insurance_CaseType', isset($cpt['id']) ? $cpt['id'] : null);
				$cpt['verification_id'] = $verification->id();
				$this->updateModel($cptModel, $cpt);
			}

			$registration->updateVerificationStatus();
			if ($registration->isAllSectionsValid()) {
				$registration->status = Registration::STATUS_SUBMIT;
			} else {
				$registration->status = Registration::STATUS_UPDATE;
			}
			$registration->save();

			$registration->case->updateStagePhase();

			$verification->commit();

			$this->result = [
				'success' => true,
				'id' => $verification->id()
			];
		}
		catch (\Exception $e) {
			$this->logSystemError($e);
			$verification->rollback();

			$this->result = [
				'success' => false,
				'errors' => explode("\n", $e->getMessage()),
				'trace' => $e->getTraceAsString()
			];
		}
	}
}
