<?php

namespace OpakeAdmin\Controller\Cases\Ajax\Intake;

use OpakeAdmin\Form\Cases\Patients\PreOperativeAdminForm;
use Opake\Helper\TimeFormat;
use Opake\Model\Cases\Registration\Reconciliation;

class PreOperative extends \OpakeAdmin\Controller\Ajax
{
	public function actionGetForm()
	{
		$registrationId = $this->request->get('registration_id');

		$preOperativeForm = $this->pixie->orm->get('Patient_Appointment_Form_PreOperativeAdmin')
			->where('case_registration_id', $registrationId)
			->find();

		if (!$preOperativeForm->loaded()) {
			$preOperativeForm = $this->pixie->orm->get('Patient_Appointment_Form_PreOperative')
				->where('case_registration_id', $registrationId)
				->find();

			$preOperativeForm->id = null;
		}

		if ($preOperativeForm->loaded()) {
			$this->result = [
				'success' => true,
				'form' => $preOperativeForm->toArray()
			];
		} else {
			$this->result = [
				'success' => false,
				'form' => null
			];
		}
	}

	public function actionSaveForm()
	{
		$registrationId = $this->request->get('registration_id');
		$model = $this->pixie->orm->get('Patient_Appointment_Form_PreOperativeAdmin')
			->where('case_registration_id', $registrationId)
			->find();

		$model->case_registration_id = $registrationId;
		$model->filled_date = TimeFormat::formatToDBDatetime(new \DateTime());

		$form = new PreOperativeAdminForm($this->pixie, $model);
		$form->load($this->getData(true));

		if ($form->isValid()) {

			$form->save();

			/** @var Reconciliation $reconciliation */
			$reconciliation = $this->pixie->orm->get('Cases_Registration_Reconciliation')
				->where('registration_id', $registrationId)
				->find();

			if (!$reconciliation->loaded()) {
				$blankReconciliation = json_decode($this->request->post('reconciliation', null, false));
				$reconciliation = $this->pixie->orm->get('Cases_Registration_Reconciliation');
				$reconciliation->fill($blankReconciliation);
				$reconciliation->registration_id = $registrationId;
				$reconciliation->save();
				$reconciliation->updateMultipleFields($blankReconciliation);
			}

			$reconciliation->updateFromPreOpForm($model);

			$this->result = [
				'success' => true,
				'id' => $model->id()
			];

		} else {

			$this->result = [
				'success' => false,
				'errors' => $form->getCommonErrorList()
			];

		}

	}
}