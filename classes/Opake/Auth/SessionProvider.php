<?php

namespace Opake\Auth;

use Opake\Auth\LoginProvider\LoginToken;
use Opake\Model\User\AbstractSessionModel;

class SessionProvider
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var string
	 */
	protected $userSessionModelName;

	protected $notRememberEnabled = true;
	protected $notRememberTime;

	protected $currentAuthSessionKey;

	/**
	 * @param \Opake\Application $pixie
	 * @param string $config
	 * @throws \Exception
	 */
	public function __construct($pixie, $config = 'default')
	{
		$configPrefix = "auth.{$config}.login.password.";

		$this->notRememberTime = $pixie->config->get($configPrefix . "not_remember_time", 0);
		$this->notRememberEnabled = $this->notRememberTime > 0;
		$this->userSessionModelName = $pixie->config->get($configPrefix . 'auth_session_model_name', 'User_Session');

		$this->pixie = $pixie;

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

	public function forceRefreshCurrentSession()
	{
		if ($this->notRememberEnabled) {
			$loginToken = $this->getLoginTokenCookie();
			$sessionHash = $loginToken->getCurrentAuthSession();

			if ($sessionHash) {
				if ($sessionModel = $this->findUserSession($sessionHash)) {
					if (!$sessionModel->isPersistSession()) {
						$expiredTime = new \DateTime();
						$expiredTime->modify('+ ' . $this->notRememberTime . ' seconds');
						$sessionModel->setExpired($expiredTime);
						$sessionModel->save();
					}
				}
			}
		}
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