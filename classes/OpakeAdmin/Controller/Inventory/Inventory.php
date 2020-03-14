<?php

namespace OpakeAdmin\Controller\Inventory;

class Inventory extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(array('/inventory/' . $this->org->id => 'Inventory'));
		$this->view->setActiveMenu('inventory.inventory');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('inventory', 'index');
		$this->view->subview = 'inventory/index';
	}

	public function actionView()
	{
		$this->checkAccess('inventory', 'view');
		$service = $this->services->get('inventory');

		$id = $this->request->param('subid');
		$inventory = $service->getItem($id);

		if (!$inventory->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->view->inventory = $inventory;
		$this->view->subview = 'inventory/view';
	}

	public function actionCreate()
	{
		$this->checkAccess('inventory', 'create');
		$this->view->subview = 'inventory/create';
	}

	public function actionReport()
	{
		$this->view->setActiveMenu('inventory.report');
		$this->view->subview = 'inventory/report';
	}
}
