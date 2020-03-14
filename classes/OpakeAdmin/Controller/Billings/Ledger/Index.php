<?php

namespace OpakeAdmin\Controller\Billings\Ledger;

class Index extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.ledger.ledger');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'view');
		$this->view->subview = 'billing/ledger/index';
	}

	public function actionView()
	{
		$this->checkAccess('billing', 'view');
		$patientId = $this->request->param('subid');
		$this->view->patientId = $patientId;
		$this->view->subview = 'billing/ledger/view';
	}

	public function actionStatementHistory()
	{
		$this->checkAccess('billing', 'view');
		$this->view->setActiveMenu('billing.ledger.statement-history');
		$this->view->subview = 'billing/ledger/statement-history';
	}
}
