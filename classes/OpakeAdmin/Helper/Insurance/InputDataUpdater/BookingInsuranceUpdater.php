<?php

namespace OpakeAdmin\Helper\Insurance\InputDataUpdater;

class BookingInsuranceUpdater
{
	protected $isModelCreate = false;

	protected $patient;

	protected $booking;

	protected $data;

	public function __construct($booking, $data, $patient, $isModelCreate = false)
	{
		$this->booking = $booking;
		$this->data = $data;
		$this->patient = $patient;
		$this->isModelCreate = $isModelCreate;
	}

	public function update()
	{
		$booking = $this->booking;
		$data = $this->data;
		$patientModel = $this->patient;

		$insurancesForSave = [];

		$app = \Opake\Application::get();

		if (!empty($data->insurances)) {
			foreach ($data->insurances as $key => $insuranceData) {
				if (empty($insuranceData->is_deleted)) {

					$insuranceModel = $app->orm->get('Booking_Insurance', (isset($insuranceData->id) ? $insuranceData->id : null));
					$insuranceModel->booking_id = $booking->id();

					$form = new \OpakeAdmin\Form\Insurance\InsuranceEditForm($app, $insuranceModel);
					$form->load($insuranceData);

					if (!$form->isValid()) {
						throw new \Opake\Exception\ValidationError($form->getFirstErrorMessage());
					}

					if (!empty($insuranceData->data)) {

						$dataModelForm = $form->getDataModelForm();
						$dataModelForm->load($insuranceData->data);

						if (!$dataModelForm->isValid()) {
							throw new \Opake\Exception\ValidationError($dataModelForm->getFirstErrorMessage());
						}

						if (isset($data->organization->id)) {
							$insuranceData->data->organization_id = $data->organization->id;
						} else if (isset($data->organization_id)) {
							$insuranceData->data->organization_id = $data->organization_id;
						}
					}

					$insuranceModel->fill($insuranceData);
					$insurancesForSave[] = [$insuranceModel];
				}
			}
		}

		if ($booking->loaded()) {
			$insuranceIds = [];
			foreach ($insurancesForSave as $saveData) {
				$insuranceModel = $saveData[0];
				if ($insuranceModel->loaded()) {
					$insuranceIds[] = $insuranceModel->id();
				}
			}

			foreach ($booking->insurances->find_all() as $num => $oldInsuranceModel) {
				if (!in_array($oldInsuranceModel->id(), $insuranceIds)) {
					$oldInsuranceModel->delete();
				}
			}
		}

		foreach ($insurancesForSave as $num => $saveData) {
			$insuranceModel = $saveData[0];

			$isNewInsurance = (!$insuranceModel->loaded());
			$insuranceModel->save();

			\OpakeAdmin\Helper\Insurance\InsurancePayorHelper::updatePayorData($insuranceModel);

			$this->updateSelectedPatientInsurance($booking, $insuranceModel, $patientModel, $isNewInsurance);
		}

		if (!empty($data->insurances)) {
			foreach ($data->insurances as $insuranceData) {
				if (!empty($insuranceData->is_deleted)) {
					$this->deletePatientInsurance($insuranceData, $patientModel);
				}
			}
		}
	}

	protected function updateSelectedPatientInsurance($booking, $insuranceModel, $patientModel, $isNewInsurance)
	{
		$app = \Opake\Application::get();

		if ($patientModel instanceof \Opake\Model\Patient) {
			$patientInsuranceModelName = 'Patient_Insurance';
			$patientInsuranceKey = 'patient_id';
		} else if ($patientModel instanceof \Opake\Model\Booking\Patient) {
			$patientInsuranceModelName = 'Booking_PatientInsurance';
			$patientInsuranceKey = 'booking_patient_id';
		} else {
			throw new \Exception('Unknown patient model');
		}

		if ($insuranceModel->selected_insurance_id) {
			/** @var \Opake\Model\Patient\Insurance $patientInsuranceModel */
			$patientInsuranceModel = $app->orm->get($patientInsuranceModelName, $insuranceModel->selected_insurance_id);
			if ($patientInsuranceModel->loaded()) {
				$patientInsuranceModel->fromBaseInsurance($insuranceModel);

				$patientInsuranceDataModel = $patientInsuranceModel->getInsuranceDataModel();
				$patientInsuranceDataModel->fromBaseInsurance($insuranceModel->getInsuranceDataModel());

				$patientInsuranceModel->save();
			}
		} else if ($isNewInsurance) {
			$newPatientInsurance = $app->orm->get($patientInsuranceModelName);
			$newPatientInsurance->{$patientInsuranceKey} = $patientModel->id();
			$newPatientInsurance->type = $insuranceModel->type;

			$patientInsuranceDataModel = $newPatientInsurance->getInsuranceDataModel();
			$patientInsuranceDataModel->fromBaseInsurance($insuranceModel->getInsuranceDataModel());

			$newPatientInsurance->save();

			$insuranceModel->selected_insurance_id = $newPatientInsurance->id();
			$insuranceModel->save();
		}

	}

	protected function deletePatientInsurance($insuranceData, $patientModel)
	{
		$app = \Opake\Application::get();

		if ($patientModel instanceof \Opake\Model\Patient) {
			$patientInsuranceModelName = 'Patient_Insurance';
		} else if ($patientModel instanceof \Opake\Model\Booking\Patient) {
			$patientInsuranceModelName = 'Booking_PatientInsurance';
		} else {
			throw new \Exception('Unknown patient model');
		}

		if (!empty($insuranceData->selected_insurance_id)) {
			$patientInsuranceModel = $app->orm->get($patientInsuranceModelName, $insuranceData->selected_insurance_id);
			if ($patientInsuranceModel->loaded()) {
				$patientInsuranceModel->delete();
			}
		}
	}
}