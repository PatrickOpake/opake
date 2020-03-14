<?php

namespace OpakeAdmin\Controller\Patients\Portal\UserDatabase;

use Opake\Exception\PageNotFound;
use OpakeAdmin\Controller\AuthPage;

class Index extends AuthPage
{
	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->setBreadcrumbs(['/patient-users/internal' => 'User Database']);
		$this->view->topMenuActive = 'patient-users';
	}

	public function actionIndex()
	{
		$this->view->subview = 'patients/portal/user-database/index';
	}

	public function actionView()
	{
		$id = $this->request->param('id');
		$patientUser = $this->orm->get('Patient_User', $id);

		if (!$patientUser->loaded()) {
			throw new PageNotFound('User not found');
		}

		$this->view->user = $patientUser;
		$this->view->addBreadCrumbs(array(
			sprintf('/patient-users/internal/view/%d/', $patientUser->id()) => $patientUser->patient->getFullName(),
		));

		$this->view->subview = 'patients/portal/user-database/view';
	}
}