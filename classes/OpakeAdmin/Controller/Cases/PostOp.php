<?php

namespace OpakeAdmin\Controller\Cases;

class PostOp extends \OpakeAdmin\Controller\AuthPage {

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->setActiveMenu('clinicals.post-op');
		$this->view->set_template('inner');
		$this->view->showSideCalendar = true;
	}

	public function actionIndex()
	{
		$this->checkAccess('registration', 'index');
		$this->view->subview = 'cases/clinicals/post_op';
	}
}
