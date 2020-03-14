<?php

namespace Console\Migration;

use Phinx\Migration\AbstractMigration;

class BaseMigration extends AbstractMigration
{
	/**
	 * @var \Opake\Application
	 */
	protected static $pixie;

	/**
	 * @return \Opake\Db
	 */
	public function getDb()
	{
		return $this->getApp()->db;
	}

	/**
	 * @return \Opake\Application
	 */
	public function getApp()
	{
		return static::getPixie();
	}

	/**
	 * @param string $message
	 */
	public function write($message)
	{
		print $message;
	}

	/**
	 * @param string $message
	 */
	public function writeln($message)
	{
		print $message . "\r\n";
	}

	/**
	 * @param \Opake\Application $pixie
	 */
	public static function initPixie($pixie)
	{
		static::$pixie = $pixie;
	}

	/**
	 * @return \Opake\Application
	 */
	public static function getPixie()
	{
		return static::$pixie;
	}
}