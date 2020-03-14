<?php

namespace OpakeAdmin\Controller\Analytics\Credentials;

use Opake\Exception\BadRequest;
use Opake\Exception\PageNotFound;
use OpakeAdmin\Controller\AuthPage;

class Credentials extends AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/analytics/credentials/' . $this->org->id => 'Analytics']);
		$this->view->set_template('inner');
	}

	public function actionMedical()
	{
		$this->checkAccess('analytics', 'view_credentials');
		$this->view->setActiveMenu('analytics.credentials.medical');
		$this->view->subview = 'analytics/credentials/medical';
	}

	public function actionNonSurgical()
	{
		$this->checkAccess('analytics', 'view_credentials');
		$this->view->setActiveMenu('analytics.credentials.non-surgical');
		$this->view->subview = 'analytics/credentials/non-surgical';
	}
}