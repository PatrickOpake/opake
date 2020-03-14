<?php

namespace OpakeAdmin\Controller\Billings\Collections;

class Collections extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.ar-management.collections');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'collections');
		$this->view->subview = 'billing/collections';
	}
}
