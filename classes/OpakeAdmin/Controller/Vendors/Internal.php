<?php

namespace OpakeAdmin\Controller\Vendors;

use OpakeAdmin\Controller\AuthPage;

class Internal extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->setBreadcrumbs(['/vendors/internal/' => 'Vendors']);
		$this->view->topMenuActive = 'vendors';
	}

	public function actionIndex()
	{
		$this->view->subview = 'vendors/index';
	}

}
