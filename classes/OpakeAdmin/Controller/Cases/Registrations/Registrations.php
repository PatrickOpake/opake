<?php

namespace OpakeAdmin\Controller\Cases\Registrations;

class Registrations extends \OpakeAdmin\Controller\AuthPage {

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/cases/registrations/' . $this->org->id => 'Registration Queue']);
		$this->view->setActiveMenu('clinicals.registration');
		$this->view->set_template('inner');
		$this->view->showSideCalendar = true;
	}

	public function actionIndex()
	{
		$this->checkAccess('registration', 'index');
		$this->view->subview = 'cases/registrations/index';
	}

	public function actionView()
	{
		$case_reg = $this->orm->get('Cases_Registration', $this->request->param('subid'));

		if (!$case_reg->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->checkAccess('cases', 'view', $case_reg);

		$this->view->id = $case_reg->id;
		$this->view->case = $case_reg->case->toArray();
		$this->view->registration = $case_reg->toArray();
		$this->view->addBreadCrumbs(['' => 'View Case Registration']);
		$this->view->subview = 'cases/registrations/view';
	}

	public function actionCreate()
	{
		$this->checkAccess('cases', 'create');

		$this->view->addBreadCrumbs(['' => 'Create Case Registration']);
		$this->view->subview = 'cases/registrations/create';
	}

}
