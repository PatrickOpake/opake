<?php

namespace OpakeAdmin\Controller\Overview;

class Dashboard extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/overview/dashboard/' . $this->org->id => 'Dashboard']);
		$this->view->setActiveMenu('schedule.dashboard');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'overview/dashboard';
		$this->view->wrapContent = false;
		$this->view->showCaseListSideCalendar = true;
	}

}
