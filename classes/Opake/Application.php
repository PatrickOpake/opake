<?php

namespace Opake;

use Opake\View\View;

/**
 * Pixie dependency container
 *
 * @inheritdoc
 * @property-read \Opake\DB $db Database module
 * @property-read \Opake\ORM $orm ORM module
 * @property-read \Opake\Config $config Config
 * @property-read \Opake\ActivityLogger $activityLogger
 * @property-read \Opake\Logger $logger
 * @property-read \Opake\EventDispatcher $events
 * @property-read \Opake\Session $session
 * @property-read \Opake\Extentions\Validate $validate
 */
class Application extends \PHPixie\Pixie
{
	const COMMON_APP_NAME = 'common';
	const COMMON_APP_NAMESPACE = "Opake\\";

	/**
	 * Root directory for the whole project stated from dir "web"
	 * e.g. /var/www/opake/web/
	 *
	 * @var string
	 */
	public $root_dir;

	/**
	 * Root directory for current application
	 * e.g. /var/www/opake/web/apps/admin/
	 *
	 * @var string
	 */
	public $app_dir;

	/**
	 * Short name of application and name of dir with assets / logs / etc.
	 * e.g. admin / api / patients
	 *
	 * @var string
	 */
	public $app_name = 'app';

	/**
	 * Current server environment
	 * e.g. dev / qa / staging / production
	 *
	 * @var string
	 */
	public $environment;

	/**
	 * Human-readable application version
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Application version with removed dots
	 *
	 * @var string
	 */
	public $version_tag;

	/**
	 * Instance definitions
	 *
	 * @var array
	 */
	protected $instance_classes = [];

	/**
	 * @var array
	 */
	protected $modules = [
		'services' => '\\Opake\\Services',
		'events' => '\\Opake\\EventDispatcher',
		'permissions' => '\\Opake\\Permissions',
		'validate' => '\\Opake\\Extentions\\Validate',
		'auth' => '\\Opake\\Auth\\AuthModule',
		'db' => '\\Opake\\DB',
		'orm' => '\\Opake\\ORM',
		'image' => '\\PHPixie\\Image',
		'session' => '\\Opake\\Session',
		'cache' => '\\PHPixie\\Cache',
		'config' => '\\Opake\\Config',
		'activityLogger' => '\\Opake\\ActivityLogger',
		'logger' => '\\Opake\\Logger',
	    'cookie' => '\\PHPixie\\Cookie',
	    'router' => '\\PHPixie\\Router',
	    'debug' => '\\Opake\\Debug'
	];

	/**
	 * @var \Opake\Application
	 */
	protected static $app;

	/**
	 * Bootstraps the project
	 *
	 * @param  string $root_dir Root directory of the application
	 * @return $this
	 */
	public function bootstrap($root_dir)
	{
		$root_dir = realpath($root_dir) . DIRECTORY_SEPARATOR;
		$this->root_dir = $root_dir;
		$this->app_dir = $root_dir . 'apps' . DIRECTORY_SEPARATOR . $this->app_name . DIRECTORY_SEPARATOR;

		if ($this->app_namespace === null) {
			$class_name = get_class($this);
			$this->app_namespace = substr($class_name, 0, strpos($class_name, "\\") + 1);
		}


		$this->init_environment();
		$this->set_asset_dirs();
		$this->init_modules();

		if ($this->config->has('app.version')) {
			$this->version = $this->config->get('app.version');
			if (file_exists($this->root_dir . '/env')) {
				$this->version_tag = md5($this->version . '|' . filemtime($this->root_dir . '/env'));
			} else {
				$this->version_tag = md5($this->version);
			}
		}


		$this->after_bootstrap();

		static::set($this);

		return $this;
	}

	protected function init_environment()
	{
		if (file_exists($this->root_dir . '/env')) {
			$environment = trim(file_get_contents($this->root_dir . '/env'));
			$this->environment = $environment;
		}

		if (!$this->environment) {
			throw new \Exception('Unknown environment, please create env file');
		}
	}

	protected function init_modules()
	{
		$firstOrderModules = [
			'config',
			'cookie',
			'debug',
			'router',
			'session',
		    'logger'
		];

		foreach ($firstOrderModules as $moduleName) {
			if (isset($this->modules[$moduleName])) {
				$class = $this->modules[$moduleName];
				$this->$moduleName = new $class($this);
			}
		}

		$this->debug->init();

		foreach ($this->config->get('routes') as $name => $rule) {
			$this->router->add($this->route($name, $rule[0], $rule[1], $this->arr($rule, 2, null)));
		}

		foreach ($this->modules as $name => $class) {
			if (!in_array($name, $firstOrderModules)) {
				$this->$name = new $class($this);
			}
		}
	}

	protected function after_bootstrap()
	{
		date_default_timezone_set($this->config->get('app.timezone'));

		if (!$this->config->get('app.debugmode')) {
			$this->debug->display_errors = false;
		} else {
			ini_set('display_errors', 'On');
			ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
		}

		// init Config helper
		Helper\Config::setPixie($this);

		session_cache_expire($this->config->get('app.session.expires') / 60);
		ini_set('session.gc_maxlifetime', $this->config->get('app.session.gc_maxlifetime'));

		/*if ($this->config->has('app.session.save_handler')) {
			ini_set('session.save_handler', $this->config->get('app.session.save_handler'));
		}
		if ($this->config->has('app.session.save_path')) {
			ini_set('session.save_path', $this->config->get('app.session.save_path', ''));
		}*/

	}

	protected function set_asset_dirs()
	{
		$this->assets_dirs = [
			$this->root_dir . 'apps/' . $this->app_name . '/assets/',
			$this->root_dir . 'apps/' . static::COMMON_APP_NAME . '/assets/'
		];
	}

	/**
	 * @param \Exception $exception
	 */
	public function handle_exception($exception)
	{
		if (!($exception instanceof \Opake\Exception\HttpException || $exception instanceof \PHPixie\Exception\PageNotFound)) {
			$this->logger->exception($exception);
		}

		$this->debug->render_exception_page($exception);
	}

	/**
	 * Constructs a request
	 *
	 * @param  Route $route Route for this request
	 * @param  string $method HTTP method for the request (e.g. GET, POST)
	 * @param  array $post Array of POST data
	 * @param  array $get Array of GET data
	 * @param  array $server Array of SERVER data
	 * @param  array $cookie Array of COOKIE data
	 * @return \Opake\Request
	 */
	public function request($route, $method = "GET", $post = array(), $get = array(), $param = array(), $server = array(), $cookie = array())
	{
		return new \Opake\Request($this, $route, $method, $post, $get, $param, $server, $cookie);
	}

	/**
	 * Constructs a response
	 *
	 * @return \PHPixie\Response
	 */
	public function response()
	{
		return new \Opake\Response($this);
	}

	public function view_helper()
	{
		return new \Opake\View\Helper($this);
	}

	/**
	 * Constructs a view
	 *
	 * @param string $name The name of the template to use
	 * @return \Opake\View\View
	 */
	public function view($name)
	{
		return new View($this, $this->view_helper(), $name);
	}

	/**
	 * @return \Opake\Application
	 */
	public static function get()
	{
		return static::$app;
	}

	/**
	 * @param \Opake\Application $app
	 */
	public static function set($app)
	{
		static::$app = $app;
	}

}
