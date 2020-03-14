<?php

namespace OpakeAdmin\Controller\Profiles;

class Profiles extends \OpakeAdmin\Controller\AuthPage
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$user = $this->logged();
		$org = $this->org;
		if ($user->isInternal()) {
			$this->redirect(sprintf('/profiles/clients/view/%d', $org->id));
		} else {
			$this->redirect(sprintf('/profiles/users/%d/view/%d/', $org->id, $user->id));
		}
	}
}
