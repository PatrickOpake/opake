<?php

namespace OpakeAdmin\Controller\Profiles;

use OpakeAdmin\Controller\Clients\Users as UsersController;

class UserProfiles extends UsersController
{
	const BASE_REDIRECT_PATH = '/profiles/users';

	public function before()
	{
		parent::before();

		$this->view->addBreadCrumbs(array('/profiles/users/' . $this->org->id => 'User profile'));
		$this->view->setActiveMenu('profile.profile');
		$this->view->baseRedirectPath = self::BASE_REDIRECT_PATH;
	}
}
