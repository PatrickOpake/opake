<?php

namespace OpakeAdmin\Helper\Insurance\InputDataUpdater;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class CodingInsuranceUpdater
{
	protected $registration;

	protected $data;

	public function __construct($registration, $data)
	{
		$this->registration = $registration;
		$this->data = $data;
	}

	public function update()
	{

		$registration = $this->registration;
		$data = $this->data;

		$app = \Opake\Application::get();

		$insurancesForSave = [];

		if (!empty($data->case_insurances)) {

			foreach ($data->case_insurances as $key => $insuranceData) {
				if (empty($insuranceData->is_deleted)) {
					$insuranceModel = $app->orm->get('Cases_Registration_Insurance', (isset($insuranceData->id) ? $insuranceData->id : null));
					$insuranceModel->registration_id = $registration->id();

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

						$insuranceData->data->organization_id = $this->registration->organization_id;

					}

					$insuranceModel->fill($insuranceData);

					$queue = null;
					$queue = $app->activityLogger->newModelActionQueue($insuranceModel);
					if ($insuranceModel->loaded()) {
						$queue->addAction(ActivityRecord::ACTION_INTAKE_EDIT_INSURANCE_INFO);
					} else {
						$queue->addAction(ActivityRecord::ACTION_INTAKE_ADD_INSURANCE);
					}
					$queue->assign();

					$insurancesForSave[] = [$insuranceModel, $queue];
				}
			}
		}

		if ($registration->loaded()) {
			$insuranceIds = [];
			foreach ($insurancesForSave as $saveData) {
				$insuranceModel = $saveData[0];
				if ($insuranceModel->loaded()) {
					$insuranceIds[] = $insuranceModel->id();
				}
			}

			foreach ($registration->insurances->where('deleted', 0)->find_all() as $num => $oldInsuranceModel) {
				if (!in_array($oldInsuranceModel->id(), $insuranceIds)) {
					$oldInsuranceModel->deleted = 1;
					$oldInsuranceModel->save();

					$queue = $app->activityLogger->newModelActionQueue($oldInsuranceModel);
					$queue->addAction(ActivityRecord::ACTION_INTAKE_REMOVE_INSURANCE);
					$queue->setAdditionalInfo('number', ($num + 1));
					$queue->assign();
					$queue->registerActions();
				}
			}
		}

		foreach ($insurancesForSave as $num => $saveData) {
			$insuranceModel = $saveData[0];
			$queue = $saveData[1];

			$isNewInsurance = (!$insuranceModel->loaded());
			$insuranceModel->save();

			\OpakeAdmin\Helper\Insurance\InsurancePayorHelper::updatePayorData($insuranceModel);

			if ($queue) {
				$queue->setAdditionalInfo('number', $num + 1);
				$queue->registerActions();
			}

			$this->updateSelectedPatientInsurance($registration, $insuranceModel, $isNewInsurance);
		}

		if (!empty($data->case_insurances)) {
			foreach ($data->case_insurances as $insuranceData) {
				if (!empty($insuranceData->is_deleted)) {
					$this->deletePatientInsurance($insuranceData);
				}
			}
		}
	}

	protected function updateSelectedPatientInsurance($registration, $insuranceModel, $isNewInsurance)
	{
		$app = \Opake\Application::get();

		if ($insuranceModel->selected_insurance_id) {
			/** @var \Opake\Model\Patient\Insurance $patientInsuranceModel */
			$patientInsuranceModel = $app->orm->get('Patient_Insurance', $insuranceModel->selected_insurance_id);
			if ($patientInsuranceModel->loaded()) {
				$patientInsuranceModel->fromBaseInsurance($insuranceModel);

				$patientInsuranceDataModel = $patientInsuranceModel->getInsuranceDataModel();
				$patientInsuranceDataModel->fromBaseInsurance($insuranceModel->getInsuranceDataModel());

				$patientInsuranceModel->save();
			}
		} else if ($isNewInsurance) {
			$newPatientInsurance = $app->orm->get('Patient_Insurance');
			$newPatientInsurance->patient_id = $registration->patient_id;
			$newPatientInsurance->type = $insuranceModel->type;

			$patientInsuranceDataModel = $newPatientInsurance->getInsuranceDataModel();
			$patientInsuranceDataModel->fromBaseInsurance($insuranceModel->getInsuranceDataModel());

			$newPatientInsurance->save();

			$insuranceModel->selected_insurance_id = $newPatientInsurance->id();
			$insuranceModel->save();
		}
	}

	protected function deletePatientInsurance($insuranceData)
	{
		$app = \Opake\Application::get();

		if (!empty($insuranceData->selected_insurance_id)) {
			$patientInsuranceModel = $app->orm->get('Patient_Insurance', $insuranceData->selected_insurance_id);
			if ($patientInsuranceModel->loaded()) {
				$patientInsuranceModel->delete();
			}
		}
	}

}