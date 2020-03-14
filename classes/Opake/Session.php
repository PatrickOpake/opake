<?php

namespace Opake;

use Opake\Helper\SessionHelper;

class Session extends \PHPixie\Session
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var int
	 */
	protected $sessionLifetime = 1800;

	/**
	 * @var string
	 */
	protected $sessionCookie = 'session';

	/**
	 * @var string
	 */
	protected $keyPrefix = '';

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
		$this->sessionLifetime = $pixie->config->get('app.session.gc_maxlifetime', $this->sessionLifetime);
		$this->sessionCookie = $pixie->config->get('app.session.cookieName', $this->sessionCookie);
	}


	public function get($key = null, $default = null)
	{
		$this->checkSession();
		$value = $this->pixie->cache->get($this->getKeyWithPrefix($key));
		return ($value !== false) ? $value : $default;
	}


	public function set($key, $val)
	{
		$this->checkSession();
		$this->pixie->cache->set($this->getKeyWithPrefix($key), $val, $this->sessionLifetime);
	}


	public function remove($key)
	{
		$this->checkSession();
		$this->pixie->cache->delete($this->getKeyWithPrefix($key));
	}


	public function reset()
	{
		$this->startNewSession();
	}

	public function flash($key, $val = null)
	{
		$this->checkSession();

		$key = "flash.{$key}";
		if ($val != null) {
			$this->set($key, $val);
		} else {
			$val = $this->get($key);
			$this->remove($key);
		}

		return $val;
	}

	protected function getKeyWithPrefix($name)
	{
		return $this->keyPrefix . '.' . $name;
	}

	protected function checkSession()
	{
		if (!$this->keyPrefix) {
			$session = $this->pixie->cookie->get($this->sessionCookie);
			if ($session) {
				$keyPrefix = 'session.' . (string) $session;
				$this->keyPrefix = $keyPrefix;

				if ($this->pixie->cache->get($this->getKeyWithPrefix('actual')) === false) {
					$this->startNewSession();
				}

			} else {
				$this->startNewSession();
			}
		}
	}

	protected function startNewSession()
	{
		$hash = SessionHelper::generateHash(42);
		$this->pixie->cookie->set($this->sessionCookie, $hash);
		$this->keyPrefix = 'session.' . $hash;
		$this->pixie->cache->set($this->getKeyWithPrefix('actual'), true, $this->sessionLifetime);
	}
}
