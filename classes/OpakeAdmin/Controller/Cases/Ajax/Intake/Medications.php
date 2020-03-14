<?php

namespace OpakeAdmin\Controller\Cases\Ajax\Intake;

use Opake\Model\Cases\Registration\Reconciliation;

class Medications extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionReconciliation()
	{
		$registrationId = $this->request->get('registration_id');

		$reconciliation = $this->pixie->orm->get('Cases_Registration_Reconciliation')
			->where('registration_id', $registrationId)
			->find();

		if ($reconciliation->loaded()) {
			$this->result = [
				'success' => true,
				'reconciliation' => $reconciliation->toArray()
			];
		} else {
			$registration = $this->orm->get('Cases_Registration', $registrationId);
			$case = $this->orm->get('Cases_Item', $registration->case_id);
			$this->result = [
				'success' => false,
				'reconciliation' => null,
				'anesthesia_type' => $case->anesthesia_type
			];
		}
	}

	public function actionSave()
	{
		$registrationId = $this->request->get('registration_id');
		$data = $this->getData();

		$model = $this->pixie->orm->get('Cases_Registration_Reconciliation')
			->where('registration_id', $registrationId)
			->find();
		$model->registration_id = $registrationId;

		try {
			if ($data) {
				$model->fill($data);
			}
			$model->save();
			$model->updateMultipleFields($data);

			$preOperativeForm = $this->pixie->orm->get('Patient_Appointment_Form_PreOperativeAdmin')
				->where('case_registration_id', $registrationId)
				->find();
			if (!$preOperativeForm->loaded()) {
				$blankPreOperativeForm = json_decode($this->request->post('pre_op_form', null, false), true);
				$preOperativeForm = $this->pixie->orm->get('Patient_Appointment_Form_PreOperativeAdmin');
				$preOperativeForm->fill($blankPreOperativeForm);
				$preOperativeForm->case_registration_id = $registrationId;
				$preOperativeForm->save();
			}
			$preOperativeForm->updateFromMedicationReconciliation($model);

			$registration = $this->orm->get('Cases_Registration', $registrationId);
			$case = $this->orm->get('Cases_Item', $registration->case_id);
			if ($case->loaded() && isset($data->case_anesthesia_type)) {
				$case->updateAnesthesiaType($data->case_anesthesia_type);
			}

		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$this->result = [
			'id' => (int) $model->id,
			'success' => true
		];
	}

	public function actionCompile()
	{
		$registrationId = $this->request->get('registration_id');

		$model = $this->pixie->orm->get('Cases_Registration_Reconciliation')
			->where('registration_id', $registrationId)
			->find();
		$model->registration_id = $registrationId;

		try {

			$helper = new \OpakeAdmin\Helper\Printing\PrintCompiler();
			$printResult = $helper->compile([
				new \OpakeAdmin\Helper\Printing\Document\Cases\ReconciliationForm($model)
			]);

			$this->result = [
				'success' => true,
				'url' => $printResult->getResultUrl(),
				'print' => $printResult->isReadyToPrint()
			];
		} catch (\Exception $e) {
			$this->logSystemError($e);
			$this->result = [
				'success' => false,
				'error' => $e->getMessage()
			];
		}
	}

}