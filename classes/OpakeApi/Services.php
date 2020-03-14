<?php

namespace OpakeApi;

class Services extends \Opake\Services
{
	/**
	 * @return array
	 */
	protected function getNamespacesForResolve()
	{
		return [
			$this->pixie->app_namespace,
			"OpakeAdmin\\",
			Application::COMMON_APP_NAMESPACE
		];
	}
}