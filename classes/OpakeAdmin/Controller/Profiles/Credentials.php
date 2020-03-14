<?php

namespace OpakeAdmin\Controller\Profiles;

class Credentials extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
		$this->view->addBreadCrumbs(array('/credentials/' . $this->org->id => 'User credentials'));
		$this->view->setActiveMenu('profile.credentials');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$credentials = $this->pixie->auth->user()->credentials;
		if($credentials->loaded()) {
			$credentials->setAlertInactive();
		}
		$this->view->subview = 'user/credentials';
	}
}
