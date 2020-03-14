<?php

namespace OpakeAdmin\Controller\Patients;

class Patients extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/patients/' . $this->org->id => 'Master Patient Index']);
		$this->view->setActiveMenu('patients.patients');
		$this->view->set_template('inner');
		$this->view->showSideCalendar = true;
	}

	public function actionIndex()
	{
		$this->view->subview = 'patients/index';
	}

	public function actionView()
	{
		$patient = $this->orm->get('Patient', $this->request->param('subid'));

		$this->checkAccess('patients', 'view', $patient);

		if (!$patient->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->view->id = $patient->id;
		$this->view->addBreadCrumbs(['' => 'View Patient']);
		$this->view->subview = 'patients/view';
	}

	public function actionCreate()
	{
		$this->checkAccess('patients', 'create');

		$this->view->addBreadCrumbs(['' => 'Create Patient']);
		$this->view->subview = 'patients/create';
	}

	public function actionImportFromExcel()
	{
		$this->checkAccess('patients', 'import_from_excel');

		if ($this->request->method === "POST") {
			$this->importPatientsFromExcel();
		}

		$this->view->subview = 'patients/import_from_excel';
	}

	protected function importPatientsFromExcel()
	{
		if (!isset($_FILES['file_import']) || $_FILES['file_import']['error']) {
			throw new \OpakeApi\Exception\BadRequest("File cant't upload");
		}
		$tmpFilename = $_FILES['file_import']['tmp_name'];
		$ext = pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION);
		$fname = $this->pixie->app_dir . '/_tmp/' . md5(microtime()) . '.' . $ext;
		move_uploaded_file($tmpFilename, $fname);

		$service = $this->services->get('Patients');
		try {
			$service->uploadFromExcel($this->org->id, $fname);
			unlink($fname);
			$this->redirect(sprintf('/patients/%d/importFromExcel', $this->org->id));
			$this->view->setMessage('Patients database has been imported');
		} catch (\Exception $e) {
			$this->logSystemError($e);
			unlink($fname);
			$this->view->errors = [$e->getMessage()];
		}
	}

}
