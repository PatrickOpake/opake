<?php

namespace OpakeAdmin\Controller\Settings\Forms\Charts;

use Opake\Model\Role;

class Index extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->setActiveMenu('settings.forms.charts');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('forms', 'view');
		$this->view->addBreadCrumbs(['/settings/forms/charts/' . $this->org->id  => 'Individual Charts']);
		if ($this->logged()->role_id == Role::FullAdmin || $this->logged()->isInternal()) {
			$this->view->subview = 'settings/forms/charts/internal';
		} else {
			$this->view->subview = 'settings/forms/charts/index';
		}
	}

	public function actionView()
	{
		$this->checkAccess('forms', 'view');
		if ($this->logged()->role_id == Role::FullAdmin || $this->logged()->isInternal()) {
			$form_id = $this->request->param('subid', null);
			if ($form_id) {
				$model = $this->loadModel('Forms_Document', 'subid');
				$this->view->model = $model;
			}
			$this->view->org = $this->org;
			$this->view->subview = 'settings/forms/charts/view';
		} else {
			throw new \Opake\Exception\Forbidden('Can\'t access to page');
		}
	}

	public function actionUploadedView()
	{
		$this->checkAccess('forms', 'view');

		$model = $this->loadModel('Forms_Document', 'subid');

		$this->view->model = $model;
		$this->view->subview = 'settings/forms/charts/uploaded_view';
	}

}
