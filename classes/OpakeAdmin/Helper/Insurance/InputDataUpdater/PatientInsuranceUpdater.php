<?php

namespace OpakeAdmin\Helper\Insurance\InputDataUpdater;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class PatientInsuranceUpdater
{
	protected $isModelCreate = false;

	protected $patient;

	protected $data;

	public function __construct($patient, $data, $isModelCreate = false)
	{
		$this->data = $data;
		$this->patient = $patient;
		$this->isModelCreate = $isModelCreate;
	}

	public function update()
	{

		$patient = $this->patient;
		$data = $this->data;
		$isModelCreate = $this->isModelCreate;

		$insurancesForSave = [];

		$app = \Opake\Application::get();

		if (!empty($data->insurances)) {

			foreach ($data->insurances as $key => $insuranceData) {
				if (empty($insuranceData->is_deleted)) {
					$insuranceModel = $app->orm->get('Patient_Insurance', (isset($insuranceData->id) ? $insuranceData->id : null));
					$insuranceModel->patient_id = $patient->id();

					$form = new \OpakeAdmin\Form\Insurance\InsuranceEditForm($app, $insuranceModel);
					$form->load($insuranceData);

					if (!$form->isValid()) {
						throw new \Opake\Exception\ValidationError($form->getFirstErrorMessage());
					}

					if (isset($insuranceData->data)) {
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

					$queue = null;
					if (!$isModelCreate) {
						$queue = $app->activityLogger->newModelActionQueue($insuranceModel);
						if ($insuranceModel->loaded()) {
							$queue->addAction(ActivityRecord::ACTION_PATIENT_EDIT_INSURANCE);
						} else {
							$queue->addAction(ActivityRecord::ACTION_PATIENT_ADD_INSURANCE);
						}
						$queue->assign();
					}

					$insurancesForSave[] = [$insuranceModel, $queue];
				}
			}
		}

		if ($patient->loaded()) {
			$insuranceIds = [];
			foreach ($insurancesForSave as $saveData) {
				$insuranceModel = $saveData[0];
				if ($insuranceModel->loaded()) {
					$insuranceIds[] = $insuranceModel->id();
				}
			}

			foreach ($patient->insurances->find_all() as $num => $oldInsuranceModel) {
				if (!in_array($oldInsuranceModel->id(), $insuranceIds)) {

					$oldInsuranceModel->delete();

					$queue = $app->activityLogger->newModelActionQueue($oldInsuranceModel);
					$queue->addAction(ActivityRecord::ACTION_PATIENT_REMOVE_INSURANCE);
					$queue->setAdditionalInfo('number', ($num + 1));
					$queue->assign();
					$queue->registerActions();
				}
			}
		}

		foreach ($insurancesForSave as $num => $saveData) {
			$insuranceModel = $saveData[0];
			$queue = $saveData[1];

			$insuranceModel->save();

			\OpakeAdmin\Helper\Insurance\InsurancePayorHelper::updatePayorData($insuranceModel);

			if ($queue) {
				$queue->setAdditionalInfo('number', $num + 1);
				$queue->registerActions();
			}
		}
	}
}