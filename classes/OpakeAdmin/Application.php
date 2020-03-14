<?php

namespace OpakeAdmin;

class Application extends \Opake\Application
{
	/**
	 * Short name of application and name of dir with assets / assets / logs / etc.
	 * @var string
	 */
	public $app_name = 'admin';

	protected $modules = [
		'services' => '\\Opake\\Services',
		'events' => '\\OpakeAdmin\\EventDispatcher',
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
		'logger' => '\\Opake\Logger',
		'cookie' => '\\PHPixie\\Cookie',
		'router' => '\\PHPixie\\Router',
		'debug' => '\\Opake\\Debug'
	];

	/**
	 * Constructs a view helper
	 *
	 * @return \PHPixie\View\Helper
	 */
	public function view_helper()
	{
		return new \OpakeAdmin\View\Helper($this);
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

	public function handle_exception($exception)
	{
		if (!($exception instanceof \Opake\Exception\HttpException || $exception instanceof \PHPixie\Exception\PageNotFound)) {
			$this->logger->exception($exception);
		}

		if ($exception instanceof \Opake\Exception\HttpException) {
			http_response_code($exception->getCode());
		}

		$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');

		if ($exception instanceof \Opake\Exception\Ajax) {
			print $exception->getJsonMessage();
		} elseif ($exception instanceof \Opake\Exception\AbstractException ||
				$exception instanceof \PHPixie\Exception\PageNotFound) {
			if ($isAjax) {
				print json_encode([
					'success' => false,
				    'code' => $exception->getCode(),
				    'message' => $exception->getMessage()
				]);
			} else {
				if ($exception instanceof \Opake\Exception\PageNotFound ||
					$exception instanceof \PHPixie\Exception\PageNotFound) {
					$view = $this->view('404');
				} else {
					$view = $this->view('error');
				}
				$view->setDefaultJsCss();
				$view->loggedUser = $this->auth->user();
				$view->message = $exception->getMessage();
				print $view->render();
			}
		} else {
			$this->handleErrorXXX($exception);
		}
	}

	protected function handleErrorXXX($exception)
	{
		$this->debug->render_exception_page($exception);
	}
}