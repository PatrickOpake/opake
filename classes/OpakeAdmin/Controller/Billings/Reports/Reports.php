<?php

namespace OpakeAdmin\Controller\Billings\Reports;

class Reports extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.ar-management.reports');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'reports');
		$this->view->subview = 'billing/reports';
	}
}
