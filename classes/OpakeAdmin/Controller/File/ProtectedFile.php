<?php

namespace OpakeAdmin\Controller\File;

use OpakeAdmin\Controller\AuthPage;
use Opake\Exception\BadRequest;
use Opake\Exception\FileNotFound;
use Opake\Exception\Forbidden;

class ProtectedFile extends AuthPage
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

		$this->view = null;
		return $this->response->file($file->mime_type, $file->original_filename, $file->readContent(), $toDownload);
	}

	public function actionGenpdf()
	{
		$doc_id = $this->request->get('id');
		$caseid = $this->request->get('caseid');
		$view = $this->pixie->view('settings/forms/charts/export/form');

		if (!$doc_id) {
			throw new BadRequest('ID is required param');
		}
		$doc = $this->orm->get('Forms_Document', $doc_id);

		if (!$doc) {
			throw new \OpakeApi\Exception\PageNotFound();
		}

		if ($caseid) {
			$case = $this->orm->get('Cases_Item', $caseid);
			if (!$case->loaded()) {
				throw new \OpakeApi\Exception\PageNotFound();
			}
			$view->case = $case;
		}

		$filename = 'chart_' . $doc->name . '.pdf';
		$view->doc = $doc;
		$view->org = $this->org;

		list($pdf, $errors) = \Opake\Helper\Export::pdf($view->render(), null, ['landscape' => $doc->is_landscape]);

		$toDownload = true;

		if ($this->request->get('to_download') === 'false') {
			$toDownload = false;
		}

		if ($errors) {
			throw new \Opake\Exception\Ajax('PDF generation failed: ' . $errors);
		} else {
			$this->response->file('application/pdf', $filename, $pdf, $toDownload);
			$this->execute = false;
		}
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

		if ($file->protected_type === 'deny') {
			return false;
		}

		if ($user->isInternal()) {
			return true;
		}

		if ($file->protected_type === 'forms') {
			return $this->checkAccessForForms($file, $user);
		}

		if ($file->protected_type === 'inventory_invoice') {
			return $this->checkAccessForInventoryInvoices($file, $user);
		}

		if ($file->protected_type === 'cases_discharge') {
			return $this->checkAccessForCasesDischarge($file, $user);
		}

		if ($file->protected_type === 'cases_registration') {
			return $this->checkAccessForCasesRegistration($file, $user);
		}

		if ($file->protected_type === 'cases_chart') {
			return $this->checkAccessForCasesChart($file, $user);
		}

		if ($file->protected_type === 'cases_financial_document') {
			return $this->checkAccessForCasesFinancialDocument($file, $user);
		}

		if ($file->protected_type === 'patient_financial_document') {
			return $this->checkAccessForPatientFinancialDocument($file, $user);
		}

		if ($file->protected_type === 'patient_chart') {
			return $this->checkAccessForPatientChart($file, $user);
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
		$form = $this->pixie->orm->get('Forms_Document');
		$form->where('uploaded_file_id', $file->id());
		$model = $form->find();
		if ($model->loaded()) {
			if ($user->organization_id == $model->organization_id) {
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
	protected function checkAccessForInventoryInvoices($file, $user)
	{
		$invoice = $this->pixie->orm->get('Inventory_Invoice');
		$invoice->where('uploaded_file_id', $file->id());
		$model = $invoice->find();
		if ($model->loaded()) {
			if ($user->organization_id == $model->organization_id) {
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
			if ($user->organization_id == $model->case->organization_id) {
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
			if ($user->organization_id == $model->case_registration->organization_id) {
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
	protected function checkAccessForCasesChart($file, $user)
	{
		$model = $this->pixie->orm->get('Cases_Chart');
		$model->query->join('case_booking_list', ['case_booking_list.id', 'case_chart.list_id'], 'inner');
		$model->where('uploaded_file_id', $file->id());
		$model = $model->find();
		if ($model->loaded()) {
			if($model->case_id) {
				$item = $this->pixie->orm->get('Cases_Item', $model->case_id);
			} else {
				$item = $this->pixie->orm->get('Booking', $model->booking_id);
			}

			if ($user->organization_id == $item->organization_id) {
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
	protected function checkAccessForCasesFinancialDocument($file, $user)
	{
		$model = $this->pixie->orm->get('Cases_FinancialDocument');
		$model->query->join('case_booking_list', ['case_booking_list.id', 'case_financial_document.list_id'], 'inner');
		$model->where('uploaded_file_id', $file->id());
		$model = $model->find();
		if ($model->loaded()) {
			if($model->case_id) {
				$item = $this->pixie->orm->get('Cases_Item', $model->case_id);
			} else {
				$item = $this->pixie->orm->get('Booking', $model->booking_id);
			}

			if ($user->organization_id == $item->organization_id) {
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
	protected function checkAccessForPatientFinancialDocument($file, $user)
	{
		$model = $this->pixie->orm->get('Patient_FinancialDocument');
		$model->query->join('patient', ['patient.id', 'patient_financial_document.patient_id'], 'inner');
		$model->where('uploaded_file_id', $file->id());
		$model = $model->find();
		if ($model->loaded()) {
			if ($user->organization_id == $model->organization_id) {
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
	protected function checkAccessForPatientChart($file, $user)
	{
		$model = $this->pixie->orm->get('Patient_Chart');
		$model->query->join('patient', ['patient.id', 'patient_chart.patient_id'], 'inner');
		$model->where('uploaded_file_id', $file->id());
		$model = $model->find();
		if ($model->loaded()) {
			if ($user->organization_id == $model->organization_id) {
				return true;
			}
		}

		return false;
	}
}