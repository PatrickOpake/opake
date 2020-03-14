<?php

namespace Opake;

class Config extends \PHPixie\Config
{
	/**
	 * Loads group from file
	 *
	 * @param string $name Name to assign the loaded group
	 * @param string $file File to load
	 */
	public function load_group($name, $file = null)
	{
		$this->loadFromFile($name);
	}

	/**
	 * Loads a group configuration file it has not been loaded before and
	 * returns its options. If the group doesn't exist creates an empty one
	 *
	 * @param string $name Name of the configuration group to load
	 * @return array    Array of options for this group
	 */
	public function get_group($name)
	{

		if (!isset($this->groups[$name])) {
			$this->loadFromFile($name);
		}

		return $this->groups[$name]['options'];
	}

	/**
	 * Writes a configuration group back to the file it was loaded from
	 *
	 * @param string $group Name of the group to write
	 * @throws \Exception
	 */
	public function write($group)
	{
		throw new \Exception('Write for configs is not supported');
	}

	/**
	 * @return bool
	 */
	public function has()
	{
		$p = func_get_args();

		$keys = explode('.', $p[0]);
		$group_name = array_shift($keys);
		$group = $this->get_group($group_name);
		if (empty($keys)) {
			return true;
		}

		$total = count($keys);
		foreach ($keys as $i => $key) {
			if (isset($group[$key])) {
				if ($i == $total - 1) {
					return true;
				}
				$group = &$group[$key];
			} else {
				if (array_key_exists(1, $p)) {
					return true;
				}
				return false;
			}
		}

		return false;
	}


	protected function loadFromFile($configName)
	{
		$confExcludes = $this->getConfigAppsExcludes();
		if (isset($confExcludes[$configName])) {
			$appName = $confExcludes[$configName];
		} else {
			$appName = $this->pixie->app_name;
		}

		$environment = $this->pixie->environment;

		$filesPriority = [
			$this->pixie->root_dir . '/apps/' . $appName . '/assets/config/local/' . $configName . '.php',
			$this->pixie->root_dir . '/apps/' . $appName . '/assets/config/' . $environment . '/'  . $configName . '.php',
			$this->pixie->root_dir . '/apps/' . $appName . '/assets/config/' . $configName . '.php',
			$this->pixie->root_dir . '/apps/' . Application::COMMON_APP_NAME . '/assets/config/local/' . $configName . '.php',
			$this->pixie->root_dir . '/apps/' . Application::COMMON_APP_NAME . '/assets/config/' . $environment . '/' . $configName . '.php',
			$this->pixie->root_dir . '/apps/' . Application::COMMON_APP_NAME . '/assets/config/' . $configName . '.php'
		];

		$resultArray = [];
		foreach (array_reverse($filesPriority) as $path) {
			$options = $this->getOptionsFromFile($path);
			if ($options !== null) {
				$resultArray = array_replace($resultArray, $options);
			}
		}

		$resultArray = $this->prepareEnvironmentVariables($resultArray, $configName);

		$this->groups[$configName] = array(
			'file' => '',
			'options' => $resultArray
		);
	}

	protected function getConfigAppsExcludes()
	{
		return [];
	}

	protected function getOptionsFromFile($file)
	{
		if (file_exists($file)) {
			$options = include($file);
			if (is_array($options)) {
				return $options;
			}
		}

		return null;
	}

	protected function prepareEnvironmentVariables($resultArray, $configName)
	{
		if ($configName === 'db') {
			if (getenv('OPAKE_DB_USER') !== false) {
				$resultArray['default']['user'] = getenv('OPAKE_DB_USER');
			}
			if (getenv('OPAKE_DB_PASSWORD') !== false) {
				$resultArray['default']['password'] = getenv('OPAKE_DB_PASSWORD');
			}
			$connectionOptions = [];
			if (getenv('OPAKE_DB_HOST') !== false) {
				$connectionOptions[] = 'host=' . getenv('OPAKE_DB_HOST');
			}
			if (getenv('OPAKE_DB_NAME') !== false) {
				$connectionOptions[] = 'dbname=' . getenv('OPAKE_DB_NAME');
			}
			if ($connectionOptions) {
				$resultArray['default']['connection'] = 'mysql:' . implode(';', $connectionOptions);
			}
		}

		return $resultArray;
	}

}