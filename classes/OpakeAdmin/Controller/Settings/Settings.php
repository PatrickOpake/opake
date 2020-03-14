<?php

namespace OpakeAdmin\Controller\Settings;

use OpakeAdmin\Controller\AuthPage;

class Settings extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->initMenu('settings');
		$this->view->getMenu()->setActiveMenu('terms');
		$this->view->setBreadcrumbs(['/settings/fields/' => 'Settings']);
		$this->view->topMenuActive = 'settings';
		$this->view->set_template('inner');
	}

	public function actionTerms()
	{
		$settings = $this->services->get('Settings');

		if ($this->request->method === 'POST') {
			$settings->updateBlockInfo('terms', $this->request->post('data', '', false));
		}
		$this->view->data = $settings->getBlockInfo('terms');

		$this->view->addBreadCrumbs([
			'/settings/terms/' => 'Terms of Service'
		]);
		$this->view->setActiveMenu('terms');
		$this->view->subview = 'settings/block_info';
	}

	public function actionPrivacy()
	{
		$settings = $this->services->get('Settings');

		if ($this->request->method === 'POST') {
			$settings->updateBlockInfo('privacy', $this->request->post('data', '', false));
		}
		$this->view->data = $settings->getBlockInfo('privacy');

		$this->view->addBreadCrumbs([
			'/settings/privacy/' => 'Privacy Policy'
		]);
		$this->view->setActiveMenu('privacy');
		$this->view->subview = 'settings/block_info';
	}

}
