<?php

namespace OpakeAdmin\Controller\Settings;

class PatientPortal extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/settings/patient-portal/' . $this->org->id => 'Patient Portal']);
		$this->view->setActiveMenu('settings.patient-portal');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->view->subview = 'settings/patient-portal/view';
	}


}