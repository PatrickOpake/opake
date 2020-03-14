<?php

namespace Opake\Auth\LoginProvider;

class LoginToken
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	protected $currentAuthSession;

	protected $cookieLifetime;

	protected $inCookies = false;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @return bool
	 */
	public function isInCookies()
	{
		return $this->inCookies;
	}

	/**
	 * @return string
	 */
	public function getCurrentAuthSession()
	{
		return $this->currentAuthSession;
	}

	/**
	 * @param string $currentAuthSession
	 */
	public function setCurrentAuthSession($currentAuthSession)
	{
		$this->currentAuthSession = $currentAuthSession;
	}

	/**
	 * @return mixed
	 */
	public function getCookieLifetime()
	{
		return $this->cookieLifetime;
	}

	/**
	 * @param mixed $cookieLifetime
	 */
	public function setCookieLifetime($cookieLifetime)
	{
		$this->cookieLifetime = $cookieLifetime;
	}

	public function extractFromCookies()
	{
		$token = $this->pixie->cookie->get('login_token', null);
		if ($token !== null) {
			$this->inCookies = true;
			//backward compatibility
			if (strpos($token, ':') !== false) {
				$tokenParts = explode(':', $token);
				if (count($tokenParts) >= 2) {
					$this->currentAuthSession = isset($tokenParts[3]) ? $tokenParts[3] : null;
				}
			} else {
				$this->currentAuthSession = $token;
			}
		} else {
			$this->inCookies = false;
		}
	}

	public function saveToCookies()
	{
		$this->pixie->cookie->set('login_token', $this->currentAuthSession, $this->cookieLifetime);
		$this->inCookies = true;
	}

	public function removeFromCookies()
	{
		if ($this->pixie->cookie->get('login_token', null)) {
			$this->pixie->cookie->remove('login_token');
		}
		$this->inCookies = false;
	}

}