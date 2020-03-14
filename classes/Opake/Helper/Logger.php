<?php

namespace Opake\Helper;

use Opake\Helper\Exception\Logger as LoggerException;

/**
 * Class Logger
 *
 * @deprecated
 * @package Opake\Helper
 */
class Logger
{

	/**
	 * Контейнер для хранения имён файлов
	 * @var array
	 */
	private static $logFiles;

	private function __construct()
	{

	}

	/**
	 * Записывает сообщение в файл
	 *
	 * @param string $type Индентификтор лога
	 * @param mixed $message Сообщение для записи в лог
	 * @param bool $needTime Нужно ли записывать время
	 * @return bool
	 */
	public static function write()
	{
		$args = func_get_args();
		list($type, $message, $needTime) = self::getArguments($args);

		if (empty(self::$logFiles[$type])) {
			return false;
		}

		$fileName = self::$logFiles[$type];

		// выдергиваем папку
		$dir = dirname($fileName);

		// если требуется, пытаемся создать папку
		if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
			return false;
		}

		if (!is_scalar($message)) {
			$message = print_r($message, true);
		}
		if ($needTime) {
			$message = "---[ " . strftime("%Y-%m-%d %H:%M:%S") . "]---\n" . $message . "\n";
		} else {
			$message .= "\n";
		}
		if ($f = fopen($fileName, 'a')) {
			fwrite($f, $message);
			fclose($f);
		}
	}

	/**
	 * Устанавливает файл, для записи лога
	 *
	 * @param string $type Индентификтор лога
	 * @param string $filename Имя файла
	 * @return void
	 */
	public static function setFile()
	{
		list($type, $filename) = self::getArguments(func_get_args());
		self::$logFiles[$type] = $filename;
	}

	/**
	 * Возвращает параметры для методов логирования
	 *
	 * @param array $args Массив параметров
	 * @return array
	 */
	private static function getArguments($args)
	{
		switch (sizeof($args)) {
			case 3:
				$type = $args[0];
				$data = $args[1];
				$needTime = $args[2];
				break;
			case 2:
				$type = $args[0];
				$data = $args[1];
				$needTime = true;
				break;
			case 1:
				$type = 'default';
				$data = $args[0];
				$needTime = true;
				break;
			default:
				throw new LoggerException("Incorrect params for Log::method([\$type,]\$fileName)", E_USER_ERROR);
		}
		return array($type, $data, $needTime);
	}

}
