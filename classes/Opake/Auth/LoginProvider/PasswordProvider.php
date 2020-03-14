<?php

namespace Opake\Auth\LoginProvider;

use Opake\Helper\TimeFormat;
use Opake\Model\User\AbstractSessionModel;

class PasswordProvider extends \PHPixie\Auth\Login\Password
{
	protected $notRememberEnabled = true;
	protected $notRememberTime;
	protected $refreshExpiresExcludes;

	protected $isAuthSessionCheckingEnabled;
	protected $currentAuthSessionKey;

	protected $userSessionModelName;

	/**
	 * @var \Opake\Auth\Repository
	 */
	protected $repository;

	/**
	 * @var \Opake\Auth\Service
	 */
	public $service;

	public function __construct($pixie, $service, $config)
	{
		parent::__construct($pixie, $service, $config);

		$this->notRememberTime = $pixie->config->get($this->config_prefix . "not_remember_time", 0);
		$this->notRememberEnabled = $this->notRememberTime > 0;
		$this->refreshExpiresExcludes = $pixie->config->get($this->config_prefix . "exclude_refresh_for_routes", false);
		$this->currentAuthSessionKey = "auth_{$config}_current_auth_session";
		$this->isAuthSessionCheckingEnabled = $pixie->config->get($this->config_prefix . 'is_auth_session_checking_enabled', false);
		$this->userSessionModelName = $pixie->config->get($this->config_prefix . 'auth_session_model_name', 'User_Session');
	}

	/**
	 * Checks if the user is logged in.
	 *
	 * @see PHPixie\Auth\Login\Provider::check_login()
	 * @return bool If the user is logged in
	 */
	public function check_login()
	{
		$token = $this->getLoginTokenCookie();

		if (!$token->isInCookies()) {
			return false;
		}

		if (!$token->getCurrentAuthSession()) {
			$this->removeAuthInfo();
			return false;
		}

		$sessionHash = $token->getCurrentAuthSession();
		$sessionModel = $this->findUserSession($sessionHash);

		if (!$sessionModel || !$sessionModel->isActive()) {
			$this->removeAuthInfo();
			return false;
		}

		if ($this->notRememberEnabled) {
			if (!$sessionModel->isPersistSession()) {
				$shouldLogout = $sessionModel->hasExpired() && $sessionModel->isSessionExpired();
				if ($shouldLogout) {
					$this->removeAuthInfo();
					return false;
				} else {
					if ($this->isRefreshExpires()) {
						//calculate new session expires time
						$updateDelayTime = AbstractSessionModel::UPDATE_EXPIRED_TIME;
						$updateExpired = false;
						if (!$sessionModel->expired) {
							$updateExpired = true;
						} else {
							$expiredDate = TimeFormat::fromDBDatetime($sessionModel->expired);
							$now = new \DateTime();
							$diffSeconds = $expiredDate->getTimestamp() - $now->getTimestamp();
							if ($diffSeconds <= 0 || $diffSeconds >= ($this->notRememberTime - $updateDelayTime)) {
								$updateExpired = true;
							}
						}

						if ($updateExpired) {
							$sessionModel->updateExpiredTime($this->notRememberTime - $updateDelayTime);
						}
					}
				}
			}
		}

		$userId = $sessionModel->getUserId();
		$user = $this->service->user_model();
		$user = $user->where($user->id_field, $userId)->find();

		if (!$user->loaded()) {
			$this->removeAuthInfo();
			return false;
		}

		$this->service->set_user($user, $this->name);
		$this->service->set_current_session($sessionModel);

		return true;
	}

	public function loginWithRememberMe($login, $password, $rememberMe = false)
	{
		return $this->login($login, $password, $rememberMe);
	}

	/**
	 * Attempts to log the user in using his login and password
	 *
	 * @param string $login Users login
	 * @param string $password Users password
	 * @param bool $persist_login Whether to persist users login.
	 *                              Defalts to false.
	 * @return bool If the user exists.
	 * @throws \Exception
	 */
	public function login($login, $password, $persist_login = false)
	{
		$user = $this->repository->get_by_login($login);

		if ($user === null)
			return false;


		if ($user instanceof \PHPixie\Auth\Login\Password\User) {
			$challenge = $user->password_hash();

		} elseif ($user instanceof \Opake\Model\AbstractModel && $user->loaded()) {
			$password_field = $this->password_field;
			$challenge = $user->$password_field;

		} else {
			return false;
		}

		if ($this->hash_method && 'crypt' == $this->hash_method) {
			if (function_exists('password_verify')) { // PHP 5.5.0+
				$password = password_verify($password, $challenge) ? $challenge : false;
			} else {
				$password = crypt($password, $challenge);
			}
		} elseif ($this->hash_method) {
			$salted = explode(':', $challenge);
			$password = hash($this->hash_method, $password . $salted[1]);
			$challenge = $salted[0];
		}

		if ($challenge === $password) {
			$this->service->set_user($user, $this->name);
			if ($this->pixie->cookie->get('login_token', null))
				$this->pixie->cookie->remove('login_token');

			$sessionModel = $this->createNewUserSessionModel($user);
			$sessionModel->setRememberMe($persist_login);

			if (!$persist_login && $this->notRememberEnabled) {
				$expiredTime = new \DateTime();
				$expiredTime->modify('+ ' . $this->notRememberTime . ' seconds');
				$sessionModel->setExpired($expiredTime);
			}

			//unique session
			if ($this->isAuthSessionCheckingEnabled) {
				$sessionModel->disableAllOtherSessionsForUser($user);
			}

			$sessionModel->save();

			$this->service->set_current_session($sessionModel);

			$loginToken = $this->getLoginTokenCookie();
			$loginToken->setCurrentAuthSession($sessionModel->getHash());
			$loginToken->setCookieLifetime($this->login_token_lifetime);
			$loginToken->saveToCookies();

			return true;
		}

		return false;

	}

	public function set_user($user)
	{
		$this->service->set_user($user, $this->name);
	}

	public function logout()
	{
		$this->removeAuthInfo();
	}

	/**
	 * @return LoginToken
	 */
	public function getLoginTokenCookie()
	{
		$token = new LoginToken($this->pixie);
		$token->extractFromCookies();

		return $token;
	}

	protected function isRefreshExpires()
	{
		$excludes = $this->refreshExpiresExcludes;

		if (!$excludes) {
			return true;
		}

		$currentRoute = $this->pixie->http_request()->route;
		return !in_array($currentRoute->name, $excludes);
	}

	protected function removeAuthInfo()
	{
		$loginToken = $this->getLoginTokenCookie();

		$sessionHash = $loginToken->getCurrentAuthSession();

		if ($sessionHash) {
			if ($sessionModel = $this->findUserSession($sessionHash)) {
				$sessionModel->setActive(false);
				$sessionModel->save();
			}
		}

		$loginToken->removeFromCookies();
	}

	/**
	 * @param $user
	 * @param $sessionKey
	 * @return bool
	 */
	protected function checkCurrentAuthSession($user, $sessionKey)
	{
		if (!$this->isAuthSessionCheckingEnabled) {
			return true;
		}

		if ($sessionModel = $this->findUserSession($sessionKey)) {
			return $sessionModel->isActive();
		}

		return false;
	}

	protected function createNewUserSessionModel($user)
	{

		/** @var AbstractSessionModel $model */
		$model = $this->pixie->orm->get($this->userSessionModelName);
		$model->setUserId($user->id());
		$model->setStarted(new \DateTime());
		$model->generateHash();

		return $model;
	}

	/**
	 * @param string $hash
	 * @return AbstractSessionModel|null
	 */
	protected function findUserSession($hash)
	{
		if (!$hash) {
			return null;
		}

		/** @var AbstractSessionModel $model */
		$model = $this->pixie->orm->get($this->userSessionModelName);
		/** @var AbstractSessionModel $sessionModel */
		$sessionModel = $model->findUserSessionByHash($hash);

		return $sessionModel;
	}
}