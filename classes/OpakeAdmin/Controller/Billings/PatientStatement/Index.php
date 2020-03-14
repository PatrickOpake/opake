<?php

namespace OpakeAdmin\Controller\Billings\PatientStatement;

class Index extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.ledger.patient-statement');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'view');
		$this->view->subview = 'billing/patient-statement/index';
	}

	public function actionView()
	{
		$this->checkAccess('billing', 'view');
		$patientId = $this->request->param('subid');
		$this->view->patientId = $patientId;
		$this->view->subview = 'billing/patient-statement/view';
	}
}
