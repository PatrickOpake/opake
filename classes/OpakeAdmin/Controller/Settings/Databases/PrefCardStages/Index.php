<?php

namespace OpakeAdmin\Controller\Settings\Databases\PrefCardStages;

use OpakeAdmin\Controller\AuthPage;

class Index extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->initMenu('settings');
		$this->view->setActiveMenu('databases.pref-card-stages');
		$this->view->topMenuActive = 'settings';
		$this->view->setBreadcrumbs([
			'/settings/fields/' => 'Settings',
			'/settings/databases/hcpc' => 'Databases',
			'' => 'Preference Card Stages'
		]);
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/databases/pref-card-stages/index';
	}

}
