<?php

namespace OpakePatients\Controller\Api;

use Opake\Model\AbstractModel;
use OpakePatients\Controller\AbstractAjax;

class Patients extends AbstractAjax
{
	public function actionPatient()
	{
		$model = $this->loadModel('Patient', 'id');
		$this->result = $model->toArray();
	}

	public function actionSave()
	{
		$service = $this->services->get('patients');
		$data = $this->getData();

		$model = $this->orm->get('Patient', isset($data->id) ? $data->id : null);

		$model->fill($data);

		$model->beginTransaction();

		$validator = $model->getValidator();
		$formErrors = [];
		if ($validator->valid()) {
			try {
				$model->save();

				$this->updatePatientInsurances($model, $data);

				$service->updateExistedRegistrations($model);

			} catch (\Exception $e) {
				$this->logSystemError($e);
				$model->rollback();
				throw new \Opake\Exception\Ajax($e->getMessage());
			}
		} else {
			$formErrors = [];
			foreach ($validator->errors() as $field => $errors) {
				$formErrors[] = implode(', ', $errors);
			}
		}
		$model->commit();

		if ($formErrors) {
			$this->result = ['errors' => $formErrors];
		} else {
			$this->result = ['id' => (int) $model->id()];
		}
	}

	public function actionSaveInsurances()
	{
		$service = $this->services->get('patients');
		$data = $this->getData();
		$model = $this->orm->get('Patient', isset($data->id) ? $data->id : null);

		$model->beginTransaction();
		try {

			$errors = [];
			if (isset($data->insurances)) {
				foreach ($data->insurances as $index => $insuranceData) {

					$insuranceModel = $this->orm->get('Patient_Insurance');
					$form = new \OpakePatients\Form\Cases\InsuranceEditForm($this->pixie, $insuranceModel);
					$form->load($insuranceData);

					if ($form->isValid()) {

						if (isset($insuranceData->data)) {
							$dataModelForm = $form->getDataModelForm();
							$dataModelForm->load($insuranceData->data);

							if (!$dataModelForm->isValid()) {
								$errors[$index + 1] = $dataModelForm->getErrors();
								continue;
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
				return;
			}

			$this->updatePatientInsurances($model, $data);
			$service->updateExistedRegistrations($model);

		} catch (\Exception $e) {
			$this->logSystemError($e);
			$model->rollback();
			throw new \Opake\Exception\Ajax($e->getMessage());
		}

		$model->commit();

		$this->result = [
			'success' => true
		];
	}

	/**
	 * @param AbstractModel $model
	 * @param \PHPixie\Validate\Validator $validator
	 * @throws \Exception
	 */
	protected function checkValidationErrors($model, $validator = null, $errorIsObject = false, $key = null, $model_name=null)
	{
		if (!$validator) {
			$validator = $model->getValidator();
		}

		if (!$validator->valid()) {
			if ($errorIsObject) {
				if (!is_null($key)) {
					$errors[$model_name][$key] = $validator->errors();
				} else {
					$errors[$model_name] = $validator->errors();
				}
				$errors['length'] = count($validator->errors());
				$error = json_encode($errors);
			} else {
				$errors_text = '';
				foreach ($validator->errors() as $field => $errors) {
					$errors_text .= implode('; ', $errors) . '; ';
				}
				$error = trim($errors_text, '; ');
			}

			throw new \Exception($error);
		}
	}

	protected function updatePatientInsurances($patient, $data)
	{

		$insurancesForSave = [];

		if (!empty($data->insurances)) {
			foreach ($data->insurances as $key => $insuranceData) {
				$insuranceModel = $this->orm->get('Patient_Insurance', (isset($insuranceData->id) ? $insuranceData->id : null));
				$insuranceModel->patient_id = $patient->id();

				if ($insuranceData) {
					$insuranceModel->fill($insuranceData);
				}

				if (!$patient->loaded()) {
					$this->checkValidationErrors($insuranceModel, null, true, ++$key, 'patient_insurance');
				}

				$insurancesForSave[] = [$insuranceModel];
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
				}
			}
		}

		foreach ($insurancesForSave as $num => $saveData) {
			$insuranceModel = $saveData[0];
			$insuranceModel->save();
		}
	}

	public function actionValidate()
	{
		$data = $this->getData();
		$service = $this->services->get('patients');
		$this->result = [
			'errors' => json_encode($service->validate('Patient', 'Patient_Insurance', $data))
		];
	}

	public function actionIsRedirectedToInsurance()
	{
		$service = $this->services->get('patients');
		$model = $this->loadModel('Patient', 'id');
		$data = json_decode(json_encode($model->toArray()));

		$validationResult = $service->validate('Patient', 'Patient_Insurance', $data);

		$this->result = !array_key_exists('patient', $validationResult);
	}

	public function actionResetInsuranceBanner()
	{
		$model = $this->loadModel('Patient', 'id');
		$model->portal_user->resetInsuranceBanner();
	}
}
