<?php

namespace Opake\Auth;

use Opake\Auth\LoginProvider\PasswordProvider;

class AuthModule extends \PHPixie\Auth
{
	/**
	 * Builds a login provider
	 *
	 * @param string $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Login\Provider  Login Provider
	 */
	public function build_login($provider, $service, $config)
	{
		if ($provider === 'password') {
			return new PasswordProvider($this->pixie, $service, $config);
		}

		return parent::build_login($provider, $service, $config);
	}


	/**
	 * Builds a service
	 *
	 * @param string  $config Configuration name of the service.
	 *                        Defaults to  'default'.
	 * @return \PHPixie\Auth\Service  Auth Service
	 */
	public function build_service($config)
	{
		return new \Opake\Auth\Service($this->pixie, $config);
	}
}