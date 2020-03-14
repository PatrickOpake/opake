<?php

namespace OpakePatients;

class Application extends \Opake\Application
{
	/**
	 * Short name of application and name of dir with assets / assets / logs / etc.
	 * @var string
	 */
	public $app_name = 'patients';

	protected $modules = [
		'services' => '\\Opake\\Services',
		'events' => '\\Opake\\EventDispatcher',
		'validate' => '\\Opake\\Extentions\\Validate',
		'auth' => '\\Opake\\Auth\\AuthModule',
		'db' => '\\Opake\\DB',
		'orm' => '\\Opake\\ORM',
		'image' => '\\PHPixie\\Image',
		'session' => '\\Opake\\Session',
		'cache' => '\\PHPixie\\Cache',
		'config' => '\\Opake\\Config',
		'logger' => '\\Opake\Logger',
		'cookie' => '\\PHPixie\\Cookie',
		'router' => '\\PHPixie\\Router',
		'debug' => '\\Opake\\Debug'
	];

	/**
	 * Constructs a view
	 *
	 * @param string $name The name of the template to use
	 * @return \Opake\View\View
	 */
	public function view($name)
	{
		return new \OpakePatients\View\View($this, $this->view_helper(), $name);
	}

	public function handle_exception($exception)
	{
		if (!($exception instanceof \Opake\Exception\HttpException || $exception instanceof \PHPixie\Exception\PageNotFound)) {
			$this->logger->exception($exception);
		}

		if ($exception instanceof \Opake\Exception\HttpException) {
			http_response_code($exception->getCode());

			if ($exception instanceof \Opake\Exception\BadRequest) {
				print $exception->getJsonData();
			}
		} else {
			$this->debug->render_exception_page($exception);
		}
	}
}