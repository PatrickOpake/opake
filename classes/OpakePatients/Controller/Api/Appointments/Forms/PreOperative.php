<?php

namespace OpakePatients\Controller\Api\Appointments\Forms;

use Opake\Helper\TimeFormat;
use Opake\Model\Cases\Registration\Reconciliation;
use OpakePatients\Controller\AbstractAjax;
use OpakePatients\Form\AppointmentForms\PreOperativeForm;

class PreOperative extends AbstractAjax
{
	public function actionGetForm()
	{
		$appointmentId = $this->request->get('appointment');

		$model = $this->pixie->orm->get('Patient_Appointment_Form_PreOperative')
			->where('case_registration_id', $appointmentId)
			->find();

		if ($model->loaded()) {
			$this->result = [
				'success' => true,
				'form' => $model->toArray()
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
		$appointmentId = $this->request->get('appointment');
		$model = $this->pixie->orm->get('Patient_Appointment_Form_PreOperative')
			->where('case_registration_id', $appointmentId)
			->find();

		$model->case_registration_id = $appointmentId;
		$model->filled_date = TimeFormat::formatToDBDatetime(new \DateTime());

		$form = new PreOperativeForm($this->pixie, $model);
		$form->load($this->getData(true));

		if ($form->isValid()) {

			$form->save();

			/** @var Reconciliation $reconciliation */
			$reconciliation = $this->pixie->orm->get('Cases_Registration_Reconciliation')
				->where('registration_id', $appointmentId)
				->find();

			if (!$reconciliation->loaded()) {
				$blankReconciliation = json_decode($this->request->post('reconciliation', null, false));
				$reconciliation = $this->pixie->orm->get('Cases_Registration_Reconciliation');
				$reconciliation->fill($blankReconciliation);
				$reconciliation->registration_id = $appointmentId;
				$reconciliation->save();
				$reconciliation->updateMultipleFields($blankReconciliation);
				$reconciliation->updateFromPreOpForm($model);
			}

			\Opake\Helper\PatientHelper::checkAppointmentFormConfirms($appointmentId);

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