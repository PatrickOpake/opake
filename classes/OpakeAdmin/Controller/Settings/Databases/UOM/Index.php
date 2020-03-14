<?php

namespace OpakeAdmin\Controller\Settings\Databases\UOM;

use OpakeAdmin\Controller\AuthPage;

class Index extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->initMenu('settings');
		$this->view->setActiveMenu('databases.uom');
		$this->view->topMenuActive = 'settings';
		$this->view->setBreadcrumbs([
			'/settings/fields/' => 'Settings',
			'/settings/databases/uom' => 'Databases',
			'' => 'Units'
		]);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/databases/uom/index';
	}

}
