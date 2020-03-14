<?php

namespace Opake\Controller\Cases\Patients\Forms;

use Opake\Helper\TimeFormat;
use Opake\Form\Cases\Patients\InfluenzaForm;

trait Influenza
{
	public function actionGetForm()
	{
		$registrationId = $this->request->get('registration_id');

		$model = $this->pixie->orm->get('Patient_Appointment_Form_Influenza')
			->where('case_registration_id', $registrationId)
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
		$this->result['illnesses_list'] = \Opake\Model\Patient\Appointment\Form\Influenza::getIllnessesFields();
	}

	public function actionSaveForm()
	{
		$registrationId = $this->request->get('registration_id');

		$model = $this->pixie->orm->get('Patient_Appointment_Form_Influenza')
			->where('case_registration_id', $registrationId)
			->find();
		$model->case_registration_id = $registrationId;
		$model->filled_date = TimeFormat::formatToDBDatetime(new \DateTime());

		$form = new InfluenzaForm($this->pixie, $model);
		$form->load($this->getData(true));

		if ($form->isValid()) {

			$form->save();

			\Opake\Helper\PatientHelper::checkAppointmentFormConfirms($registrationId);

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