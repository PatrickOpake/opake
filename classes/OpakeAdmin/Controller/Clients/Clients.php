<?php

namespace OpakeAdmin\Controller\Clients;

class Clients extends \OpakeAdmin\Controller\AuthPage
{
	const BASE_REDIRECT_PATH = '/clients/sites';

	public function before()
	{
		parent::before();
		$this->view->addJS('/js/clients.js', false);
		$this->view->topMenuActive = 'clients';
		$this->view->setActiveMenu('settings.organization');
		$this->view->baseRedirectPath = self::BASE_REDIRECT_PATH;
	}

	/**
	 * Список организаций
	 */
	public function actionIndex()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->topMenuActive = null;
		$this->view->setActiveMenu('');
		$this->view->subview = 'clients/index';
	}

	/**
	 * Просмотр органзиации
	 */
	public function actionView()
	{
		$id = $this->request->param('id');
		$this->iniOrganization($id);
		$this->view->setActiveMenu('settings.organization.details');
		$this->view->addBreadCrumbs(['/' => 'Details']);
		$this->view->subview = 'clients/view';
		$this->view->org = $this->org;
		$this->view->set_template('inner');
	}

	public function actionCreate()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->topMenuActive = null;
		$this->view->setActiveMenu('');

		$service = $this->services->get('Clients');
		$org = $service->getItem();

		$this->view->org = $org;
		$this->view->addBreadCrumbs(['' => 'Create New Site']);
		$this->view->subview = 'clients/create';
	}

	public function actionUsers()
	{
		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}
		$this->view->addBreadCrumbs(['/clients/users/' => 'Users']);

		$service = $this->services->get('Clients_Users');
		$model = $service->getItem();

		$search = new \OpakeAdmin\Model\Search\User($this->pixie);
		$results = $search->search($model, $this->request);

		$this->view->list = $results;
		$this->view->pages = $search->getPagination();
		$this->view->filters = $search->getParams();
		$this->view->subview = 'clients/users';
	}

}
