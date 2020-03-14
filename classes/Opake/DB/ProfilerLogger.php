<?php

namespace Opake\DB;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class ProfilerLogger extends \Opake\Logger
{
	/**
	 * @return \Monolog\Logger
	 */
	protected function initLogger()
	{
		$logger = new \Monolog\Logger('application');
		$handler = new StreamHandler($this->getLogPath('db.log'), \Monolog\Logger::INFO);
		$formatter = new LineFormatter();
		$formatter->includeStacktraces(true);
		$formatter->allowInlineLineBreaks(true);
		$handler->setFormatter($formatter);
		$logger->pushHandler($handler);

		$logger->info('');
		$logger->info('Profiler started: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''));

		return $logger;
	}

}