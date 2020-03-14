<?php

namespace Opake\Helper;

use Opake\Application;

class PatientHelper
{
	public static function checkAppointmentFormConfirms($appointmentId)
	{
		$pixie = Application::get();

		$preOperativeForm = $pixie->orm->get('Patient_Appointment_Form_PreOperative')
			->where('case_registration_id', $appointmentId)
			->find();


		$influenzaForm = $pixie->orm->get('Patient_Appointment_Form_Influenza')
			->where('case_registration_id', $appointmentId)
			->find();

		if ($preOperativeForm->loaded() && $influenzaForm->loaded()) {


			$confirmModel = $pixie->orm->get('Patient_Appointment_Confirm')
				->where('case_registration_id', $appointmentId)
				->find();

			if (!$confirmModel->loaded()) {
				$user = $pixie->auth->user();

				$confirmModel = $pixie->orm->get('Patient_Appointment_Confirm');
				$confirmModel->patient_user_id = $user->id();
				$confirmModel->case_registration_id = $appointmentId;
			}

			$confirmModel->is_forms_confirmed = 1;
			$confirmModel->save();
		}
	}
}