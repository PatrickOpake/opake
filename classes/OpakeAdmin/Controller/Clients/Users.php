<?php

namespace OpakeAdmin\Controller\Clients;

class Users extends \OpakeAdmin\Controller\AuthPage
{

	const MSG_USER_SAVED = 'User saved';
	const MSG_USER_SAVED_AND_EMAIL = 'User saved and PWD email sent';
	const MSG_PWD_EMAIL_SENT = 'Password setup email sent';
	const BASE_REDIRECT_PATH = '/clients/users';

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(array('/clients/users/' . $this->org->id => 'Users'));
		$this->view->setActiveMenu('settings.organization.users');
		$this->view->topMenuActive = 'clients';
		$this->view->set_template('inner');
		// TODO: разгрести по трезвой
		$this->view->addJS('/js/clients.js', false);
		$this->view->baseRedirectPath = self::BASE_REDIRECT_PATH;
	}

	/**
	 * Список пользователей
	 */
	public function actionIndex()
	{
		if (!$this->getAccessLevel('user', 'view')->isAllowed()) {
			throw new \Opake\Exception\Forbidden();
		}
		$this->view->subview = 'clients/users/index';
	}

	/**
	 * Информация о пользователе
	 */
	public function actionView()
	{
		$service = $this->services->get('Clients_Users');

		$id = $this->request->param('subid');
		$user = $service->getItem($id);

		$this->checkAccess('user', 'view', $user);

		if (!$user->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$site = $this->services->get('Clients_Sites')->getItem($this->request->get('site'));
		if (!$site->loaded()) {
			$site = $user->sites->find();
		}

		$this->view->user = $user;
		$this->view->addBreadCrumbs(array(
			sprintf('/clients/sites/%d/view/%d/', $this->org->id, $site->id) => $site->name,
			sprintf('/clients/users/%d/view/%d/', $this->org->id, $user->id) => $user->getFullName(),
		));
		$this->view->subview = 'clients/users/view';
	}

	public function actionCreate()
	{
		$this->checkAccess('user', 'create');

		$service = $this->services->get('Clients_Users');
		$service_sites = $this->services->get('Clients_Sites');
		$user = $service->getItem();
		$user->organization_id = $this->org->id;

		$site_id = $this->request->get('site');
		$site = $service_sites->getItem($site_id);

		$curr_sites = [];
		if ($site->loaded()) {
			$this->view->addBreadCrumbs([sprintf('/clients/sites/%d/view/%d/', $this->org->id, $site_id) => $site->name]);
			$curr_sites[] = $site_id;
		}

		$this->view->addBreadCrumbs(['' => 'Create New User']);
		$this->view->subview = 'clients/users/create';
	}

	public function actionSendpwd()
	{
		$this->checkAccess('user', 'edit');

		$orgId = $this->request->param('id');
		$userId = $this->request->param('subid');

		/* @var $user \Opake\Model\User */
		$user = $this->orm->get('user')->where('id', $userId)->find();

		// Set unique hash for user
		$user->setHash();
		$user->save();

		// Sending mail
		$mailer = new \Opake\Helper\Mailer();
		$mailer->sendPwdEmail($user);

		$this->flash('message', self::MSG_PWD_EMAIL_SENT);
		$this->redirect(sprintf('%s/%d/view/%d/', static::BASE_REDIRECT_PATH, $orgId, $userId));
	}

}
