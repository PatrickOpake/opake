<?php

namespace Opake;

use Opake\Request\RequestUploadedFile;

class Request extends \PHPixie\Request
{
	/**
	 * @var RequestUploadedFile[]
	 */
	protected $files;

	/**
	 * Initializes the routed Controller and executes specified action
	 *
	 * @return \PHPixie\Response A Response object with the body and headers set
	 */
	public function execute()
	{
		$this->pixie->cookie->set_cookie_data($this->_cookie);
		$class = $this->param('namespace', $this->pixie->app_namespace) . 'Controller\\' . ucfirst($this->param('controller'));
		if (!class_exists($class, true)) {
			throw new \Opake\Exception\PageNotFound();
		}
		$controller = $this->pixie->controller($class);
		$controller->request = $this;
		$controller->run($this->param('action'));
		return $controller->response;
	}

	/**
	 * Returns request files
	 *
	 * @return RequestUploadedFile[]
	 */
	public function getFiles()
	{
		if ($this->files === null) {
			$this->files = RequestUploadedFile::getRequestFiles();
		}

		return $this->files;
	}

}
