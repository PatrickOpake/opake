<?php

namespace OpakeAdmin\Controller\Settings;

class SmsTemplate extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/sms-template/' . $this->org->id => 'SMS Template']);
		$this->view->setActiveMenu('settings.templates.sms-template');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/sms-template/index';
	}
}