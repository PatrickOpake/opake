<?php

namespace OpakeAdmin\Controller\Clients;

class Sites extends \OpakeAdmin\Controller\AuthPage
{
	const BASE_REDIRECT_PATH = '/clients';

	public function before()
	{
		parent::before();

		$this->checkAccess('user', 'view');
		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(array('/clients/sites/' . $this->org->id => 'Sites'));
		$this->view->setActiveMenu('settings.organization.sites');
		$this->view->topMenuActive = 'clients';
		$this->view->set_template('inner');

		// TODO: разгрести по трезвой
		$this->view->addJS('/js/clients.js', false);
		$this->view->baseRedirectPath = self::BASE_REDIRECT_PATH;
	}

	/**
	 * Список мест организации
	 */
	public function actionIndex()
	{
		$this->checkAccess('sites', 'view');
		$this->view->subview = 'clients/sites/index';
	}

	public function actionView()
	{
		$this->checkAccess('sites', 'view');
		$service = $this->services->get('Clients_Sites');

		$id = $this->request->param('subid');
		$site = $service->getItem($id);

		if (!$site->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		\OpakeAdmin\Helper\Menu\SiteNavigationMenu::prepareMenu($this->view, $id);
		$this->view->setActiveMenu('settings.organization.sites.profile');

		$this->view->site = $site;
		$this->view->addBreadCrumbs([
			sprintf('/clients/sites/%d/view/%d/', $this->org->id, $site->id) => $site->name
		]);
		$this->view->subview = 'clients/sites/view';
	}


	public function actionCreate()
	{
		$this->checkAccess('sites', 'create');
		$this->view->addBreadCrumbs(['' => 'Create New Site']);
		$this->view->subview = 'clients/sites/create';
	}

}
