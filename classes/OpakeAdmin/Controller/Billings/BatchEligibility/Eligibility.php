<?php

namespace OpakeAdmin\Controller\Billings\BatchEligibility;

class Eligibility extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.batch-eligibility');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'eligibility');
		$this->view->subview = 'billing/batch-eligibility';
	}
}
