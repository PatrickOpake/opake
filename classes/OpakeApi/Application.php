<?php

namespace OpakeApi;

use Opake\Helper\Config;
use Opake\Helper\Logger;
use Opake\Helper\Net;

/**
 * Pixie dependency container
 *
 * @property-read \PHPixie\DB $db Database module
 * @property-read \PHPixie\ORM $orm ORM module
 */
class Application extends \Opake\Application
{

	/**
	 * Short name of application and name of dir with assets / assets / logs / etc.
	 * @var string
	 */
	public $app_name = 'api';


	protected $modules = array(
		'services' => '\\OpakeApi\\Services',
		'events' => '\\Opake\\EventDispatcher',
		'permissions' => '\\Opake\\Permissions',
		'validate' => '\\Opake\\Extentions\\Validate',
		'auth' => '\\Opake\\Auth\\AuthModule',
		'db' => '\\Opake\\DB',
		'orm' => '\\OpakeApi\\ORM',
		'image' => '\\PHPixie\\Image',
		'session' => '\\Opake\\Session',
		'config' => '\\OpakeApi\\Config',
		'activityLogger' => '\\Opake\\ActivityLogger',
		'logger' => '\\OpakeApi\Logger',
		'cookie' => '\\PHPixie\\Cookie',
		'router' => '\\PHPixie\\Router',
		'debug' => '\\Opake\\Debug'
	);

	/**
	 * Creates a Request representing current HTTP request.
	 *
	 * @return \PHPixie\Request
	 */
	public function http_request()
	{
		$uri = filter_input(INPUT_GET, 'route');
		if ($uri !== null) {
			$uri = '/' . trim($uri, '/');
			$route_data = $this->router->match($uri, $_SERVER['REQUEST_METHOD']);
			return $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'], $_POST, $_GET, $route_data['params'], $_SERVER, $_COOKIE);
		}

		return parent::http_request();
	}

	protected function after_bootstrap()
	{
		// init Config helper
		Config::setPixie($this);
		date_default_timezone_set(Config::get('app.timezone'));
		if (!Config::get('app.debugmode')) {
			$this->debug->display_errors = false;
		}

		session_cache_expire($this->config->get('app.session.expires') / 60);
		ini_set('session.gc_maxlifetime', $this->config->get('app.session.gc_maxlifetime'));

		/*if ($this->config->has('app.session.save_handler')) {
			ini_set('session.save_handler', $this->config->get('app.session.save_handler'));
		}
		if ($this->config->has('app.session.save_path')) {
			ini_set('session.save_path', $this->config->get('app.session.save_path', ''));
		}*/
	}

	public function handle_exception($exception)
	{
		if (!($exception instanceof \Opake\Exception\HttpException || $exception instanceof \PHPixie\Exception\PageNotFound
			|| $exception instanceof \OpakeApi\Exception\HttpException)) {
			$this->logger->exception($exception);
		}

		if ($exception instanceof \OpakeApi\Exception\AbstractException) {
			print $exception->getJsonMessage();
		} else {
			$this->handleErrorXXX($exception);
		}
	}

	protected function handleErrorXXX($exception)
	{
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
	 * @return \PHPixie\Request
	 */
	public function request($route, $method = "GET", $post = array(), $get = array(), $param = array(), $server = array(), $cookie = array())
	{
		return new \OpakeApi\Request\Request($this, $route, $method, $post, $get, $param, $server, $cookie);
	}

}
