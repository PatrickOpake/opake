<?php

namespace OpakeAdmin\Controller;

class Dev extends AbstractController
{

	public function before()
	{
		if (!$this->request->is_ajax()) {
			$this->view = $this->pixie->view('dev/template');
			$this->view->addCSSList([
				'/common/vendors/bootstrap/css/bootstrap.min.css',
				'/css/dev.css'
			]);

			$this->view->addJsList([
				'/common/vendors/jquery/jquery-2.1.1.min.js' => true,
				'/js/dev.js' => false
			]);
		}
	}

	public function after()
	{
		$this->response->body = $this->view->render();
	}

	public function actionIndex()
	{
		$this->view->message = 'Welcome';
	}

	public function actionMain()
	{
		$this->view->logs = array_map('basename', glob($this->pixie->root_dir . '/logs/*'));
		$this->view->subview = 'main';
	}

	public function actionLog()
	{
		$filename = $this->pixie->root_dir . '/logs/' . $this->request->post('file');
		if (file_exists($filename)) {
			$this->response->body = nl2br(file_get_contents($filename));
		}
	}

	public function actionLogClear()
	{
		array_map('unlink', glob($this->pixie->root_dir . '/logs/*'));
	}
	public function actionDemovoice(){
		$this->view->addJS('https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js');
		setcookie('NUSA_Guids', 'c1b39dfa-3d8d-4c86-830d-73d6233910c5/01688fe7-a01c-4f93-98e3-0e1ededdc682');
		$this->view->addJS('https://speechanywhere.nuancehdp.com/2.1/scripts/Nuance.SpeechAnywhere.js');
		$this->view->subview = 'demovoice';
	}

}
