<?php

namespace OpakeAdmin\Controller\Settings;

class Alerts extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();
		$this->checkAccess('alerts', 'settings');
		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/settings/alerts/' . $this->org->id => 'Alerts']);
		$this->view->setActiveMenu('settings.alerts');
		$this->view->set_template('inner');

	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/alerts/index';
	}

	public function actionView()
	{
		$this->view->siteId = $this->request->param('subid');
		$this->view->subview = 'settings/alerts/view';
	}


}