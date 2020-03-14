<?php

namespace Opake;

class DB extends \PHPixie\DB
{

	/**
	 * @param string $config
	 * @return mixed
	 */
	public function get($config = 'default')
	{
		if (!isset($this->db_instances[$config])) {
			$driver = $this->pixie->config->get("db.{$config}.driver");
			$driver = "\\Opake\\DB\\".$driver."\\Connection";
			$this->db_instances[$config] = new $driver($this->pixie, $config);
		}
		return $this->db_instances[$config];
	}

	/**
	 * @param string $config
	 * @return mixed
	 */
	public function refresh_connection($config = 'default')
	{
		if (isset($this->db_instances[$config])) {
			unset($this->db_instances[$config]);
		}

		return $this->get($config);
	}

	public function begin_transaction($config = 'default')
	{
		$this->get($config)->execute('START TRANSACTION');
	}

	public function rollback($config = 'default')
	{
		$this->get($config)->execute('ROLLBACK');
	}

	public function commit($config = 'default')
	{
		$this->get($config)->execute('COMMIT');
	}

	public function arr($array, $config = 'default')
	{
		if (!is_array($array)) {
			throw new \Exception('Passed value is not an array');
		}

		$origConnection = $this->get($config)->conn;
		$escapedValues = [];
		foreach ($array as $value) {
			$escapedValue = $origConnection->quote($value);
			if ($escapedValue === false) {
				$this->pixie->logger->warning('Database driver doesn\'t support escaping');
				$escapedValue = $value;
			}
			$escapedValues[] = $escapedValue;
		}

		return $this->expr('(' . implode(', ', $escapedValues) . ')');
	}
}