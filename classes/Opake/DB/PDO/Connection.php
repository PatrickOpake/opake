<?php

namespace Opake\DB\PDO;

use Opake\DB\ProfilerLogger;

class Connection extends \PHPixie\DB\PDO\Connection
{
	/**
	 * @var bool
	 */
	protected $isProfilerEnabled = false;

	/**
	 * @var ProfilerLogger
	 */
	protected $profilerLogger;

	/**
	 * @param \PHPixie\Pixie $pixie
	 * @param string $config
	 */
	public function __construct($pixie, $config)
	{
		$this->pixie = $pixie;
		$options = [];

		if ($pixie->config->has("db.{$config}.mysql_ssl_key")) {
			$options[\PDO::MYSQL_ATTR_SSL_KEY] = $pixie->config->get("db.{$config}.mysql_ssl_key");
		}
		if ($pixie->config->has("db.{$config}.mysql_ssl_cert")) {
			$options[\PDO::MYSQL_ATTR_SSL_CERT] = $pixie->config->get("db.{$config}.mysql_ssl_cert");
		}
		if ($pixie->config->has("db.{$config}.mysql_ssl_ca")) {
			$options[\PDO::MYSQL_ATTR_SSL_CA] = $pixie->config->get("db.{$config}.mysql_ssl_ca");
		}
		if ($pixie->config->has("db.{$config}.mysql_init_command")) {
			$options[\PDO::MYSQL_ATTR_INIT_COMMAND] = $pixie->config->get("db.{$config}.mysql_init_command");
		}

		$this->conn = new \PDO(
			$pixie->config->get("db.{$config}.connection"),
			$pixie->config->get("db.{$config}.user", ''),
			$pixie->config->get("db.{$config}.password", ''),
			$options
		);

		$this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->db_type = strtolower(str_replace('PDO_', '', $this->conn->getAttribute(\PDO::ATTR_DRIVER_NAME)));
		if ($this->db_type != 'sqlite') {
			$this->conn->exec("SET NAMES 'utf8'");
		}

		if ($pixie->config->has("db.{$config}.profiler")) {
			$this->isProfilerEnabled = $pixie->config->get("db.{$config}.profiler");
		}

		$this->initProfiling();
	}

	/**
	 * @return ProfilerLogger
	 */
	protected function getProfilerLogger()
	{
		if (!$this->profilerLogger) {
			$this->profilerLogger = new ProfilerLogger($this->pixie);
		}

		return $this->profilerLogger;
	}

	protected function initProfiling()
	{
		if ($this->isProfilerEnabled) {
			$this->execute("SET profiling_history_size=100");
			$this->execute("SET PROFILING=1");
		}
	}

	public function __destruct()
	{
		if ($this->isProfilerEnabled) {
			$message = "Profiler results:\r\n";
			$result = $this->execute("SHOW PROFILES")->as_array();
			foreach ($result as $row) {
				$message .= $row->Query_ID . "\t\t" . $row->Duration . "\t\t" . $row->Query . "\r\n";
			}
			$this->getProfilerLogger()->info($message);
		}
	}
}