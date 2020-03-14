<?php

namespace Console;

use Opake\Helper\Config;

class Application extends \Opake\Application
{

	public $app_namespace = 'OpakeAdmin\\';
	public $app_name = 'admin';

	protected $modules = array(
		'services' => '\\Opake\\Services',
		'events' => '\\OpakeAdmin\\EventDispatcher',
		'db' => '\\Opake\\DB',
		'orm' => '\\Opake\\ORM',
		'config' => '\\Opake\\Config',
		'logger' => '\\Console\\Logger',
		'cookie' => '\\PHPixie\\Cookie',
		'router' => '\\PHPixie\\Router',
		'debug' => '\\Opake\\Debug'
	);

	protected function after_bootstrap()
	{
		date_default_timezone_set($this->config->get('app.timezone'));
		Config::setPixie($this);

		ini_set('display_errors', 'On');
		ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_STRICT);
	}

	/**
	 * Constructs a view
	 *
	 * @param string $name The name of the template to use
	 * @return \Opake\View\View
	 */
	public function view($name)
	{
		return new \OpakeAdmin\View\View($this, $this->view_helper(), $name);
	}

	/**
	 * Constructs a view helper
	 *
	 * @return \PHPixie\View\Helper
	 */
	public function view_helper()
	{
		return new \OpakeAdmin\View\Helper($this);
	}

}
