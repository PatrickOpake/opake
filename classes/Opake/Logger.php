<?php

namespace Opake;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class Logger
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var \Monolog\Logger
	 */
	protected $originalLogger;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;
		$this->originalLogger = $this->initLogger();
	}

	/**
	 * @return \Monolog\Logger
	 */
	public function getOriginalLogger()
	{
		return $this->originalLogger;
	}

	public function log($level, $message, array $context = array())
	{
		$level = \Monolog\Logger::toMonologLevel($level);

		return $this->originalLogger->addRecord($level, $message, $context);
	}

	public function debug($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::DEBUG, $message, $context);
	}


	public function info($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::INFO, $message, $context);
	}


	public function notice($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::NOTICE, $message, $context);
	}


	public function warn($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::WARNING, $message, $context);
	}


	public function warning($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::WARNING, $message, $context);
	}


	public function err($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::ERROR, $message, $context);
	}


	public function error($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::ERROR, $message, $context);
	}


	public function crit($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::CRITICAL, $message, $context);
	}


	public function critical($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::CRITICAL, $message, $context);
	}


	public function alert($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::ALERT, $message, $context);
	}


	public function emerg($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::EMERGENCY, $message, $context);
	}


	public function emergency($message, array $context = array())
	{
		return $this->originalLogger->addRecord(\Monolog\Logger::EMERGENCY, $message, $context);
	}

	/**
	 * @param \Exception $exception
	 * @return bool
	 */
	public function exception($exception, array $context = array())
	{
		return $this->error((string) $exception , $context);
	}

	protected function initLogger()
	{
		$logger = new \Monolog\Logger('application');
		$handler = new StreamHandler($this->getLogPath($this->getLogFileName()), \Monolog\Logger::INFO);
		$formatter = new LineFormatter();
		$formatter->includeStacktraces(true);
		$formatter->allowInlineLineBreaks(true);
		$handler->setFormatter($formatter);
		$logger->pushHandler($handler);

		return $logger;
	}

	protected function getLogFileName()
	{
		return 'application.log';
	}

	protected function getLogPath($logFileName)
	{
		if ($this->pixie->config->has('app.log_dir') && $this->pixie->config->get('app.log_dir')) {
			$appLogDir = rtrim($this->pixie->config->get('app.log_dir'), '/');
		} else {
			$appLogDir = $this->pixie->app_dir . 'logs';
		}

		return $appLogDir . '/' . $logFileName;
	}
}