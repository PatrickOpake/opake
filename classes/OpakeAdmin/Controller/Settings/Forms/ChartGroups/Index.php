<?php

namespace OpakeAdmin\Controller\Settings\Forms\ChartGroups;

class Index extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
		$this->view->addBreadCrumbs(['/settings/forms/chart-groups/' . $this->org->id  => 'Chart Groups']);
		$this->view->setActiveMenu('settings.forms.chart-groups');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/forms/chart-groups/index';
	}
}