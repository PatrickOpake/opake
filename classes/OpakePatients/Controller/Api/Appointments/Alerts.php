<?php

namespace OpakePatients\Controller\Api\Appointments;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;
use OpakePatients\Controller\AbstractAjax;
use OpakePatients\Model\Appointment\AlertType\AbstractType;

class Alerts extends AbstractAjax
{
	public function actionGetAlerts()
	{
		$user = $this->pixie->auth->user();
		if (!$user) {
			throw new Forbidden();
		}

		$query = $this->pixie->db->query('select');
		$query->table('case_registration');
		$query->fields('case_registration.id');
		$query->join('case', ['case_registration.case_id', 'case.id']);
		$query->join('patient_user_appointment_confirm', ['patient_user_appointment_confirm.case_registration_id', 'case_registration.id']);
		$query->join('patient_user_appointment_alert_hidden', ['patient_user_appointment_alert_hidden.case_registration_id', 'case_registration.id']);
		$query->where('case_registration.patient_id', $user->patient->id());
		$query->where([
			['patient_user_appointment_confirm.id', '', $this->pixie->db->expr('IS NULL')],
			['or', [['patient_user_appointment_confirm.is_patient_info_confirmed', 0]]],
			['or', [['patient_user_appointment_confirm.is_insurances_confirmed', 0]]],
			['or', [['patient_user_appointment_confirm.is_forms_confirmed', 0]]],
		]);
		$query->where([
			['patient_user_appointment_alert_hidden.id', '', $this->pixie->db->expr('IS NULL')],
			['or', [['patient_user_appointment_alert_hidden.is_info_alert_hidden', 0]]],
			['or', [['patient_user_appointment_alert_hidden.is_insurance_alert_hidden', 0]]],
			['or', [['patient_user_appointment_alert_hidden.is_forms_alert_hidden', 0]]],
		]);
		$query->order_by('case.time_start', 'DESC');

		$foundRow = [];
		foreach ($query->execute() as $row) {
			$foundRow[] = $row->id;
		}

		$alertsLimit = 10;
		$result = [];
		if ($foundRow) {
			foreach ($foundRow as $id) {
				$model = $this->pixie->orm->get('Cases_Registration', $id);
				if ($model->loaded()) {
					$result = array_merge($result, $this->fetchAppointmentAlerts($model));
				}

				if (count($result) >= $alertsLimit) {
					$result = array_splice($result, 0, 10);
					break;
				}
			}
		}

		$this->result = [
			'success' => true,
			'result' => $result
		];
	}

	public function actionHideAlert()
	{
		$registration = $this->findRegistration($this->request->post('id'));
		$type = $this->request->post('type');

		if (!$type) {
			throw new BadRequest('Type param is required');
		}

		$obj = AbstractType::getObjectByType($type, $this->pixie, $registration);
		$obj->hideAlert();

		$this->result = [
			'success' => true
		];
	}

	protected function findRegistration($id)
	{
		if (!$id) {
			throw new BadRequest('ID param is required');
		}

		$user = $this->pixie->auth->user();
		if (!$user) {
			throw new Forbidden();
		}

		$model = $this->orm->get('Cases_Registration');

		$modelQuery = $model->query;
		$modelQuery->where('patient_id', $user->patient->id());
		$modelQuery->where('id', $id);

		$registration = $model->find();

		if (!$registration->loaded()) {
			throw new PageNotFound();
		}

		return $registration;
	}

	protected function fetchAppointmentAlerts($registration)
	{
		$showAlertsForTypes = [
			AbstractType::TYPE_PATIENT_INFO,
			AbstractType::TYPE_INSURANCES,
			AbstractType::TYPE_FORMS
		];

		$result = [];
		foreach ($showAlertsForTypes as $type) {
			$obj = AbstractType::getObjectByType($type, $this->pixie, $registration);
			if ($obj->isNeedToShowAlert()) {
				$result[] = $obj->getAlertData();
			}
		}

		return $result;
	}
}