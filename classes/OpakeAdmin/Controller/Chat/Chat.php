<?php

namespace OpakeAdmin\Controller\Chat;

use OpakeAdmin\Controller\AuthPage;

class Chat extends AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/chat/' . $this->org->id => 'Chat Log']);
		$this->view->setActiveMenu('chat');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('chat', 'view_history');
		$this->view->subview = 'chat/list';
	}
}
