<?php

namespace OpakeAdmin\Controller\Inventory\Invoices;

class Invoices extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/inventory/invoices/' . $this->org->id => '/Invoices']);
		$this->view->setActiveMenu('inventory.invoices');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'inventory/invoices/index';
	}

	public function actionView()
	{
		$this->view->model = $this->loadModel('Inventory_Invoice', 'subid');
		$this->view->subview = 'inventory/invoices/view';
	}

}
