<?php

namespace OpakeAdmin\Controller\Billings;

class Billings extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/billings/' . $this->org->id => 'Billings']);
		$this->view->setActiveMenu('billing.billing');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('billing', 'index');
		$this->view->subview = 'billing/index';
	}

	public function actionView()
	{
		$this->checkAccess('billing', 'view');
		$case = $this->orm->get('Cases_Item', $this->request->param('subid'));

		if (!$case->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}
		$this->view->id = $case->registration->id;
		$this->view->case = $case->toArray();
		$this->view->registration = $case->registration->toArray();
		$this->view->subview = 'billing/view';
	}

}
