<?php

namespace OpakeAdmin\Controller;

class Page extends AuthPage
{

	public function actionPt()
	{
		$settings = $this->services->get('Settings');
		$this->view->terms = $settings->getBlockInfo('terms');
		$this->view->privacy = $settings->getBlockInfo('privacy');
		$this->view->subview = 'page/pt';
	}

	public function actionContact()
	{
		$this->view->subview = 'page/contact';
	}

	public function actionEditor(){
		$this->iniDictation();
		$this->view->subview = 'page/editor';
	}
}
