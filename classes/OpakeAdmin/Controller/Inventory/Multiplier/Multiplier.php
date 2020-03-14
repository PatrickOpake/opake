<?php

namespace OpakeAdmin\Controller\Inventory\Multiplier;

class Multiplier extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/inventory-multiplier/' . $this->org->id => 'Inventory']);
		$this->view->setActiveMenu('settings.databases.inventory-multiplier');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('inventory', 'index');
		$this->view->subview = 'inventory/multipliers';
	}
}
