<?php

namespace OpakeAdmin\Controller\Cases;

class Discharge extends \OpakeAdmin\Controller\AuthPage {

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->setActiveMenu('clinicals.discharge');
		$this->view->set_template('inner');
		$this->view->showSideCalendar = true;
	}

	public function actionIndex()
	{
		$this->checkAccess('registration', 'index');
		$this->view->subview = 'cases/clinicals/discharge';
	}
}
