<?php

namespace OpakeAdmin\Controller\Billings\EOB;

class EOB extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.ar-management.eob_management');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'eob');
		$this->view->subview = 'billing/eob/index';
	}
}
