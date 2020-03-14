<?php

namespace OpakeAdmin\Controller\Verification;

use Opake\Model\Cases\Registration;

class Verification extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/verification/' . $this->org->id => 'Verification & Pre-Authorization']);
		$this->view->setActiveMenu('schedule.verification');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('verifications', 'index');
		$this->view->subview = 'verification/index';
	}

	public function actionView()
	{
		$this->checkAccess('verifications', 'view');
		/** @var Registration $registration */
		$registration = $this->orm->get('Cases_Registration', $this->request->param('subid'));

		if (!$registration->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->view->id = $registration->id;
		$this->view->case = $registration->case->toArray();
		$this->view->registration = $registration->toArray();
		$this->view->subview = 'verification/view';
		$this->view->showSideCalendar = true;
	}

}
