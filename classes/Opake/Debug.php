<?php

namespace Opake;

class Debug extends \PHPixie\Debug
{
	/**
	 * Converts PHP Errors to Exceptions
	 *
	 * @param string        $errno   Error number
	 * @param string        $errstr  Error message
	 * @param string        $errfile File in which the error occurred
	 * @param string        $errline Line at which the error occurred
	 * @return void
	 * @throws \ErrorException Throws converted exception to be immediately caught
	 */
	public function error_handler($errno, $errstr, $errfile, $errline)
	{
		$exception = new  \ErrorException($errstr, $errno, 0, $errfile, $errline);
		if (!$this->pixie->config->get('app.debugmode')) {
			if ($errno == E_NOTICE) {
				$this->pixie->logger->notice((string) $exception);
				return;
			}
			if ($errno == E_WARNING) {
				$this->pixie->logger->warning((string) $exception);
				return;
			}
		}

		throw $exception;
	}

	public function shutdown_handler()
	{
		$error = error_get_last();
		if (!empty($error['type']) && ($error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR))) {
			while (ob_get_level()) {
				ob_end_clean();
			}

			try {
				$this->error_handler($error['type'], $error['message'], $error['file'], $error['line']);
			} catch (\Exception $e) {
				$this->pixie->logger->exception($e);
				if (php_sapi_name() !== 'cli') {
					$this->render_exception_page($e);
				}
			}
		} else {
			if (ob_get_length()) {
				ob_end_flush();
			}
		}
	}

	/**
	 * Initializes the error handler
	 *
	 * @return void
	 */
	public function init()
	{
		set_error_handler([$this, 'error_handler'], E_ALL);
		register_shutdown_function([$this, 'shutdown_handler']);
	}
}