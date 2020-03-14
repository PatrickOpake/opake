<?php

namespace OpakeAdmin\Controller;

use Opake\Helper\Config;

/**
 * Abstract pages with autherization required
 */
abstract class AuthPage extends AbstractController
{

	/**
	 * Logged user
	 *
	 * @var \Opake\Model\User
	 */
	protected $user;

	/**
	 * Controller's view
	 *
	 * @var \OpakeAdmin\View\View
	 */
	protected $view;

	public function before()
	{
		/* @var $user \Opake\Model\User */
		if ($user = $this->logged()) {
			$user->updateLoginTime();
			$this->view = $this->pixie->view('main');
			$this->view->setDefaultJsCss();
			$this->view->loggedUser = $user;
			$this->view->setBreadcrumbs($user->isInternal() ? ['/clients' => 'Organizations'] : []);
		} else {
			$this->redirect('/');
			$this->response->send_headers();
			exit;
		}
	}

	public function after()
	{
		if ($this->view) {
			if($user = $this->logged()) {
				$user->setLastVisitedPage($this->request->server('REQUEST_URI'));
			}
			$this->response->disableCache();
			$this->response->body = $this->view->render();
		}
	}

	protected function iniOrganization($id)
	{
		parent::iniOrganization($id);

		$this->view->org = $this->org;
		$this->view->addBreadCrumbs(['/clients/view/' . $this->org->id => $this->org->name]);
	}

	protected function iniDictation()
	{
		if ($this->getAccessLevel('operative_reports', 'dragon_dictation')->isDisallowed()) {
			return;
		}
		$user = $this->logged();
		if (!$user->is_dictation_enabled) {
			return;
		}
		$organization = $user->organization;
		$params = Config::get('app.dragon_dictation');
		if (!$params['enable'] || !$organization->nuance_org_id) {
			return;
		}

		setcookie('NUSA_Guids', $organization->nuance_org_id . '/01688fe7-a01c-4f93-98e3-0e1ededdc682');
		$this->view->addJS('https://speechanywhere.nuancehdp.com/2.1/scripts/Nuance.SpeechAnywhere.js');
		$this->view->dictationEnabled = true;
		$this->view->dragonApplicationName = $params['application_name'];
		$this->view->dragonUserId = $user->username ? $user->username : $user->email;
	}

}
