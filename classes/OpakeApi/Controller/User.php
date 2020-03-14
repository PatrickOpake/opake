<?php

namespace OpakeApi\Controller;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class User extends AbstractController
{

	public function actionEmpty()
	{

	}

	public function actionLogin()
	{
		$login = $this->request->post('user', null, false);
		$password = $this->request->post('pwd', null, false);

		if (!$login || !$password) {
			throw new \OpakeApi\Exception\BadRequest("'user' and 'pwd' expected");
		}

		if ($this->pixie->auth->provider('password')->login($login, $password) && $this->logged()->status === \Opake\Model\User::STATUS_ACTIVE) {
			$this->pixie->activityLogger
				->newAction(ActivityRecord::ACTION_AUTH_LOGIN)
				->register();
			$this->result = $this->logged()->toArray();
		} else {
			if ($this->logged()) {
				$this->pixie->auth->provider('password')->logout();
			}
			throw new \OpakeApi\Exception\Forbidden();
		}
	}

	public function actionLogout()
	{
		$this->pixie->auth->provider('password')->logout();
		$this->pixie->activityLogger
			->newAction(ActivityRecord::ACTION_AUTH_LOGOUT)
			->register();
	}

	public function actionList()
	{
		$this->result = [];
		$users = $this->logged()->organization->users;

		if ($this->request->get('profession')) {
			$profession = json_decode($this->request->get('profession'));
			if (is_array($profession)) {
				$users->where('profession_id', 'IN', $this->pixie->db->expr('(' . implode(', ', $profession) . ')'));

			} else {
				$users->where('profession_id', $profession);
			}
		}

		foreach ($users->find_all() as $user) {
			$this->result[] = $user->toArray(false);
		}
	}

	public function actionDetails()
	{
		$user = $this->loadModel('User', 'id');
		$this->result = $user->toArray();
	}

	public function actionPermissions()
	{
		$user = $this->loadModel('user', 'id');
		$this->result = $this->pixie->permissions->getInspectorForUser($user)->getPermissionConfig();
	}

	public function actionResetpwd()
	{
		$mail = $this->request->get('mail');

		if (!$mail) {
			throw new \OpakeApi\Exception\BadRequest('\'mail\' expected');
		}

		$user = $this->orm->get('User')->where('email', $mail)->find();

		if (!$user->loaded()) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		// Set unique hash for user
		$user->setHash();
		$user->save();

		// Sending mail
		$mailer = new \Opake\Helper\Mailer();
		$mailer->sendPwdEmail($user);
		$this->message = 'Password reset email sent';
	}

}
