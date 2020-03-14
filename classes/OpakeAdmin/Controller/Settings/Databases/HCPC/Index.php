<?php

namespace OpakeAdmin\Controller\Settings\Databases\HCPC;

use OpakeAdmin\Controller\AuthPage;
use Opake\Exception\BadRequest;

class Index extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->initMenu('settings');
		$this->view->setActiveMenu('databases.hcpc');
		$this->view->topMenuActive = 'settings';
		$this->view->setBreadcrumbs([
			'/settings/fields/' => 'Settings',
			'/settings/databases/hcpc' => 'Databases',
			'' => 'HCPCs'
		]);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/databases/hcpcs/index';
	}

	public function actionViewYear()
	{
		$this->view->year_id = $this->request->param('id');
		$this->view->subview = 'settings/databases/hcpcs/view-year';
	}

}
