<?php

namespace OpakePatients\Controller;

use Opake\Exception\BadRequest;
use Opake\Exception\FileNotFound;
use Opake\Exception\Forbidden;

class File extends \Opake\Controller\AbstractController
{
	/**
	 * @return mixed
	 * @throws BadRequest
	 * @throws FileNotFound
	 * @throws Forbidden
	 */
	public function actionView()
	{
		$id = $this->request->get('id');
		$toDownload = true;

		if ($this->request->get('to_download') === 'false') {
			$toDownload = false;
		}

		if (!$id) {
			throw new BadRequest('ID is required param');
		}

		/** @var \Opake\Model\UploadedFile $file */
		$file = $this->pixie->orm->get('UploadedFile', $id);
		if (!$file) {
			throw new FileNotFound();
		}

		if (!$this->checkAccessToFile($file)) {
			throw new Forbidden();
		}

		return $this->response->file($file->mime_type, $file->original_filename, $file->readContent(), $toDownload);
	}

	/**
	 * @param \Opake\Model\UploadedFile $file
	 * @return bool
	 */
	protected function checkAccessToFile($file)
	{
		$user = $this->logged();
		if (!$user) {
			return false;
		}

		if ($file->protected_type === 'forms') {
			return $this->checkAccessForForms($file, $user);
		}

		if ($file->protected_type === 'cases_discharge') {
			return $this->checkAccessForCasesDischarge($file, $user);
		}

		if ($file->protected_type === 'cases_registration') {
			return $this->checkAccessForCasesRegistration($file, $user);
		}

		return true;
	}

	/**
	 * @param \Opake\Model\UploadedFile $file
	 * @param \Opake\Model\User $user
	 * @return bool
	 */
	protected function checkAccessForForms($file, $user)
	{
		return false;
	}

	/**
	 * @param \Opake\Model\UploadedFile $file
	 * @param \Opake\Model\User $user
	 * @return bool
	 */
	protected function checkAccessForCasesDischarge($file, $user)
	{
		return $this->checkAccessForCaseRelatedModel('Cases_Discharge', $file, $user);
	}

	/**
	 * @param string $modelName
	 * @param \Opake\Model\UploadedFile $file
	 * @param \Opake\Model\User $user
	 * @return bool
	 */
	protected function checkAccessForCaseRelatedModel($modelName, $file, $user)
	{
		$optionModel = $this->pixie->orm->get($modelName);
		$optionModel->with('case');
		$optionModel->where('uploaded_file_id', $file->id());
		$model = $optionModel->find();

		if ($model->loaded() && $model->case->loaded()) {
			if ($user->patient->id() == $model->case->registration->patient_id) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param \Opake\Model\UploadedFile $file
	 * @param \Opake\Model\User $user
	 * @return bool
	 */
	protected function checkAccessForCasesRegistration($file, $user)
	{
		$model = $this->pixie->orm->get('Cases_Registration_Document');
		$model->with('case_registration');
		$model->where('uploaded_file_id', $file->id());
		$model = $model->find();
		if ($model->loaded()) {
			if ($user->patient->id() == $model->case_registration->patient_id) {
				return true;
			}
		}

		return false;
	}
}