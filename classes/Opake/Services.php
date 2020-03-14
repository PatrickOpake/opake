<?php

/**
 * Доступ к сервисам приложения
 */

namespace Opake;

class Services
{

	protected $pixie;
	protected $instances = array();

	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	/**
	 * @param $name
	 * @return null
	 * @throws \Exception
	 */
	public function get($name)
	{
		if (isset($this->instances[$name])) {
			return $this->instances[$name];
		} else {
			foreach ($this->getNamespacesForResolve() as $namespace) {
				if ($service = $this->tryToLoadService($namespace, $name)) {
					$this->instances[$name] = $service;
					return $service;
				}
			}
		}

		throw new \Exception('Unknown service: ' . $name);
	}

	/**
	 * @param string $namespace
	 * @param string $name
	 * @return null
	 */
	protected function tryToLoadService($namespace, $name)
	{
		$service = $namespace . 'Service\\' . implode('\\', array_map('ucfirst', explode('_', $name)));
		if (!class_exists($service, true)) {
			$service .= '\\' . ucfirst($name);
			if (!class_exists($service, true)) {
				return null;
			}
		}

		return new $service($this->pixie);
	}

	/**
	 * @return array
	 */
	protected function getNamespacesForResolve()
	{
		return [
			$this->pixie->app_namespace,
			Application::COMMON_APP_NAMESPACE
		];
	}

}
