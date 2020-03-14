<?php

namespace OpakeAdmin\Controller;

use Opake\Model\Analytics\UserActivity\ActivityRecord;

class Auth extends AbstractController
{

	const MSG_ERROR_LOGIN = 'Sorry, this username/password combination doesn\'t match our records. Please try again.';
	const MSG_ERROR_EMPTY = 'Please provide both username and password to login';
	const MSG_ERROR_INACTIVE = self::MSG_ERROR_LOGIN;

	public function actionLogin()
	{
		$this->response->headers = [
			'Content-Type: application/json'
		];

		$validate = $this->pixie->validate->get($this->request->post());
		$validate->field('login')->rule('filled');
		$validate->field('password')->rule('filled');

		if ($validate->valid()) {
			$email = $this->request->post('login', null, false);
			$password = $this->request->post('password', null, false);
			$remember = $this->request->post('remember');

			if (!$this->pixie->auth->provider('password')->login($email, $password, $remember) || $this->logged()->status !== \Opake\Model\User::STATUS_ACTIVE) {
				if ($this->logged() && $this->logged()->status === \Opake\Model\User::STATUS_INACTIVE) {
					$error = self::MSG_ERROR_INACTIVE;
				} else {
					$error = self::MSG_ERROR_LOGIN;
				}

				if ($this->logged()) {
					$this->pixie->auth->provider('password')->logout();
				}
			} else {
				$this->pixie->activityLogger
					->newAction(ActivityRecord::ACTION_AUTH_LOGIN)
					->register();
			}
		} else {
			$error = self::MSG_ERROR_EMPTY;
		}

		$this->response->body = json_encode(isset($error) ? ['success' => false, 'error' => $error] : ['success' => true]);
	}

	public function actionLogout()
	{
		if ($this->logged()) {
			$this->pixie->activityLogger
				->newAction(ActivityRecord::ACTION_AUTH_LOGOUT)
				->register();

			$auth = $this->pixie->auth;
			$auth->logout();
		}
		$this->redirect('/');
	}

}
