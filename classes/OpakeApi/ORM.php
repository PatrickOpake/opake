<?php

namespace OpakeApi;

class ORM extends \Opake\ORM
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