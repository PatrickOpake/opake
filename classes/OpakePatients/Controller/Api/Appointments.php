<?php

namespace OpakePatients\Controller\Api;

use Opake\Exception\BadRequest;
use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;
use Opake\Helper\Pagination;
use OpakePatients\Controller\AbstractAjax;

class Appointments extends AbstractAjax
{

	public function actionMyAppointments()
	{
		$user = $this->pixie->auth->user();
		if (!$user) {
			throw new Forbidden();
		}

		$model = $this->orm->get('Cases_Registration');

		$modelQuery = $model->query;
		$modelQuery->where('patient_id', $user->patient->id());

		$modelQuery->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));
		$modelQuery->join('case', [$model->table . '.case_id', 'case.id']);
		/*$startDate = (new \DateTime())->format('Y-m-d') . ' 00:00:00';
		$modelQuery->where([
			[$model->table . '.status', '<>', \Opake\Model\Cases\Registration::STATUS_SUBMIT],
			['or', [
				['case.time_start', '>=', $startDate]
			]
			]
		]);*/

		$sort = $this->request->get('sort_by', 'dos');
		$order = $this->request->get('sort_order', 'DESC');

		switch ($sort) {
			case 'acc_number':
				$model->order_by('case.id', $order);
				break;
			case 'dos':
				$model->order_by('case.time_start', $order);
				break;
			case 'first_name':
				$model->order_by($model->table . '.first_name', $order);
				break;
			case 'last_name':
				$model->order_by($model->table . '.last_name', $order);
				break;
			case 'appointment':
				$model->order_by($this->pixie->db->expr('TIME(case.time_start)'), $order);
				break;
			case 'dob':
				$model->order_by('patient.dob', $order);
				break;
			case 'status':
				$model->order_by($model->table . '.status', $order);
				break;
			case 'procedure':
				$model->query->join('case_type', ['case.type_id', 'case_type.id']);
				$model->order_by('case_type.name', $order);
				break;
		}

		$pagination = new Pagination();
		$pagination->setPage($this->request->get('p'));
		$pagination->setLimit($this->request->get('l'));

		$pagination->setCount($model->count_all());
		$results = $model->pagination($pagination)->find_all()->as_array();

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $pagination->getCount()
		];
	}

	public function actionGet()
	{
		$this->result = [
			'success' => true,
			'registration' => $this->findRegistration($this->request->get('id'))->toArray()
		];
	}

	public function actionSavePatientInfo()
	{
		$registration = $this->findRegistration($this->request->get('id'));

		$data = $this->getData(true);

		$form = new \OpakePatients\Form\Cases\RegistrationInfoEditForm($this->pixie, $registration);
		$form->load($data);

		if ($form->isValid()) {
			$form->save();

			if ($registration->patient_id) {
				$patient = $this->pixie->orm->get('Patient', $registration->patient_id);
				$patient->fromRegistration($registration);
				$patient->save();
				$service = $this->services->get('Patients');
				$service->updateExistedRegistrations($patient);
			}

			$this->result = [
				'success' => true,
				'registration' => $registration->toArray()
			];
		} else {
			$this->result = [
				'success' => false,
				'errors' => $form->getErrors()
			];
		}
	}

	public function actionSaveInsurances()
	{
		$registration = $this->findRegistration($this->request->get('id'));

		$data = $this->getData(true);
		$errors = [];

		$this->pixie->db->begin_transaction();

		try {
			if (isset($data['insurances'])) {
				foreach ($data['insurances'] as $index => $insuranceData) {

					$insuranceModel = $this->orm->get('Cases_Registration_Insurance', isset($insuranceData['id']) ? $insuranceData['id'] : null);
					$insuranceModel->registration_id = $registration->id();

					$form = new \OpakePatients\Form\Cases\InsuranceEditForm($this->pixie, $insuranceModel, $registration);
					$form->load($insuranceData);

					if ($form->isValid()) {

						if (isset($insuranceData['data'])) {
							$dataModelForm = $form->getDataModelForm();
							$dataModelForm->load($insuranceData['data']);

							if (!$dataModelForm->isValid()) {
								$errors[$index+1] = $dataModelForm->getErrors();
								continue;
							}

							$dataModelForm->save();
						}


						$form->save();


						if ($insuranceModel->selected_insurance_id) {
							/** @var \Opake\Model\Patient\Insurance $patientInsuranceModel */
							$patientInsuranceModel = $this->orm->get('Patient_Insurance', $insuranceModel->selected_insurance_id);
							if ($patientInsuranceModel->loaded()) {
								$patientInsuranceModel->fromBaseInsurance($insuranceModel);

								$patientInsuranceDataModel = $patientInsuranceModel->getInsuranceDataModel();
								$patientInsuranceDataModel->fromBaseInsurance($insuranceModel->getInsuranceDataModel());

								$patientInsuranceModel->save();
							}
						}
					} else {
						$errors[$index+1] = $form->getErrors();
					}

				}
			}

			if ($errors) {

				$this->pixie->db->rollback();
				$this->result = [
					'success' => false,
					'errors' => $errors
				];
			} else {

				$this->pixie->db->commit();
				$this->result = [
					'success' => true,
					'registration' => $registration->toArray()
				];
			}
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}

	}

	public function actionConfirmPatientInfo()
	{
		$registration = $this->findRegistration($this->request->get('id'));
		$data = $this->getData(true);
		$form = new \OpakePatients\Form\Cases\RegistrationInfoEditForm($this->pixie, $registration);
		$form->load($data);

		if (!$form->isValid()) {
			$this->result = [
				'success' => false,
				'errors' => $form->getErrors()
			];
			return;
		}

		if ($registration->patient_confirm && $registration->patient_confirm->loaded()) {
			$patientConfirm = $registration->patient_confirm;
		} else {
			$user = $this->pixie->auth->user();
			$patientConfirm = $this->orm->get('Patient_Appointment_Confirm');
			$patientConfirm->patient_user_id = $user->id();
			$patientConfirm->case_registration_id = $registration->id();
		}

		$patientConfirm->is_patient_info_confirmed = 1;
		$patientConfirm->save();

		$this->result = [
			'success' => true
		];
	}

	public function actionConfirmInsurances()
	{
		$registration = $this->findRegistration($this->request->get('id'));

		if ($registration->patient_confirm && $registration->patient_confirm->loaded()) {
			$patientConfirm = $registration->patient_confirm;
		} else {
			$user = $this->pixie->auth->user();
			$patientConfirm = $this->orm->get('Patient_Appointment_Confirm');
			$patientConfirm->patient_user_id = $user->id();
			$patientConfirm->case_registration_id = $registration->id();
		}

		$patientConfirm->is_insurances_confirmed = 1;
		$patientConfirm->save();

		$this->result = [
			'success' => true
		];
	}

	public function actionConfirmForms()
	{

	}

	protected function confirmAppointment($field)
	{

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
}