<?php

namespace OpakeAdmin\Controller\Billings\ClaimsManagement;

class Index extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->setActiveMenu('billing.claims-management.electronic-claims');
		$this->checkAccess('billing', 'index');
		$this->view->subview = 'billing/claims-management';
	}

	public function actionPaperClaims()
	{
		$this->view->setActiveMenu('billing.claims-management.paper-claims');
		$this->checkAccess('billing', 'index');
		$this->view->subview = 'billing/paper-claims';
	}
}