<?php

namespace OpakeAdmin\Controller\Analytics\SmsLog;

use OpakeAdmin\Controller\AuthPage;

class SmsLog extends AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/analytics/reports/' . $this->org->id => 'Analytics']);
		$this->view->setActiveMenu('analytics.sms-log');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('analytics', 'view_sms_log');
		$this->view->subview = 'analytics/sms-log/index';
	}

}
