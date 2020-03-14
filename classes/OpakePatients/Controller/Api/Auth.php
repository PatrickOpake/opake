<?php

namespace OpakePatients\Controller\Api;

use Opake\Auth\SessionProvider;
use Opake\Exception\Forbidden;
use Opake\Exception\PageNotFound;
use OpakePatients\Controller\AbstractAjax;
use OpakePatients\Exception\Authentication;

class Auth extends AbstractAjax
{

	public function actionLogin()
	{
		$data = $this->getData(true);
		$validate = $this->pixie->validate->get($data);
		$validate->field('login')->rule('filled');
		$validate->field('password')->rule('filled');

		if ($validate->valid()) {
			if (!$this->pixie->auth->provider('password')->login($data['login'], $data['password'])) {
				throw Authentication::incorrectPassword();
			}

			$user = $this->logged();
			if ($user && $user->patient->loaded() && $user->patient->organization->loaded() && $user->patient->organization->portal->loaded()) {
				if (!$user->patient->organization->portal->isPublished()) {
					throw new Forbidden();
				}
			}

			$user->updateLoginDates();

			$this->result = [
				'change_password' => (int) $user->is_tmp_password
			];
		} else {
			throw Authentication::formIsNotFilled();
		}
	}

	public function actionUser()
	{
		$user = $this->logged();
		if (!$user || !$user->loaded() || $user->is_tmp_password) {
			throw new \Opake\Exception\Unauthorized();
		}
		$this->result = $user->toArray();
	}

	public function actionChangePassword()
	{
		$user = $this->logged();
		if (!$user || !$user->loaded()) {
			throw new \Opake\Exception\Unauthorized();
		}

		$data = $this->getData(true);
		$password = (isset($data['password'])) ? $data['password'] : '';
		$passwordConfirm = (isset($data['password_confirm'])) ? $data['password_confirm'] : '';

		$validator = $user->getPasswordValidator($password, $passwordConfirm);

		if ($validator->valid() && $user->is_tmp_password) {
			$user->setPassword($data['password']);
			$user->is_tmp_password = false;
			$user->save();
		} else {
			$errors = $validator->errors()['new_password'];
			$errorMessage = reset($errors);

			throw new Authentication($errorMessage);
		}
	}

	public function actionLogout()
	{
		if ($this->logged()) {
			$auth = $this->pixie->auth;
			$auth->logout();
		}
	}

	public function actionRefreshExpires()
	{
		$sessionProvider = new SessionProvider($this->pixie);
		$sessionProvider->forceRefreshCurrentSession();

		$this->result = [
			'success' => true
		];
	}

	public function actionKeepActive()
	{
		$this->logged();

		$this->result = [
			'success' => true
		];
	}

	public function actionCheckLoggedIn()
	{
		$this->result = [
			'success' => true,
			'logged' => $this->pixie->auth->user() != null
		];
	}

}
