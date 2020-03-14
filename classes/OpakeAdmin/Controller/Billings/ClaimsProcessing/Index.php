<?php

namespace OpakeAdmin\Controller\Billings\ClaimsProcessing;

class Index extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.claims-processing');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'index');
		$this->view->subview = 'billing/claims-processing';
	}
}