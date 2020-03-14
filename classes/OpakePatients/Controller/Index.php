<?php

namespace OpakePatients\Controller;

class Index extends \Opake\Controller\AbstractController
{

	public function before()
	{
		$this->view = $this->pixie->view('main');
		$this->view->setDefaultJsCss();
	}

	public function after()
	{
		$this->response->body = $this->view->render();
	}

	public function actionIndex()
	{
		$alias = $this->request->param('portal_alias');
		if ($alias) {
			$portal = $this->orm->get('Patient_Portal')->where('alias', $alias)->find();
			if ($portal->isPublished()) {
				$this->view->portal = $portal;
			}
		}
	}

}
