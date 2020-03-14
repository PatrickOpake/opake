<?php

namespace Opake\Helper;

/**
 * Class Config
 *
 * @deprecated
 * @package Opake\Helper
 */
class Config
{

	/**
	 * DI pattern
	 * @var \Opake\Application
	 */
	protected static $pixie;

	/**
	 * Инициализирует конфиг, прокидывая в него основное приложение (DI pattern)
	 * @param \Opake\Application $pixie
	 */
	public static function setPixie($pixie)
	{
		self::$pixie = $pixie;
	}

	public static function get($param)
	{
		// check in db
		// ...
		return self::$pixie->config->get($param);
	}

	public static function set($param, $value)
	{
		self::$pixie->config->set($param, $value);
	}

	public static function has($param)
	{
		return self::$pixie->config->has($param);
	}

}
