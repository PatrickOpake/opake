<?php

namespace OpakeAdmin\Controller\Profiles;

use OpakeAdmin\Controller\Clients\Clients as ClientsController;

class ClientProfiles extends ClientsController
{
	const BASE_REDIRECT_PATH = '/profiles/clients';

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
		$this->view->addBreadCrumbs(array('/profiles/clients/view/' . $this->org->id => 'Organization profile'));
		$this->view->setActiveMenu('profile.profile');
		$this->view->baseRedirectPath = self::BASE_REDIRECT_PATH;
	}

}
