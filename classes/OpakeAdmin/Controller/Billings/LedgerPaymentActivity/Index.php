<?php

namespace OpakeAdmin\Controller\Billings\LedgerPaymentActivity;

class Index extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.ledger.payment-activity');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'view');
		$this->view->subview = 'billing/ledger-payment-activity/index';
	}
}
