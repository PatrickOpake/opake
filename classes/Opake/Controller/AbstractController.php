<?php

namespace Opake\Controller;

abstract class AbstractController extends \PHPixie\Controller
{

	/**
	 * DI Pattern
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * ORM module
	 * @var \Opake\ORM
	 */
	protected $orm;

	/**
	 * Request for this controller. Holds all input data.
	 * @var \Opake\Request
	 */
	public $request;

	/**
	 * Response for this controller. It will be updated with headers and
	 * response body during controller execution
	 * @var \Opake\Response
	 */
	public $response;

	/**
	 * Services
	 * @var \Opake\Services
	 */
	protected $services;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		parent::__construct($pixie);
		$this->orm = $this->pixie->orm;
		$this->services = $this->pixie->services;
	}

	/**
	 * Runs the appropriate action.
	 * It will execute the before() method before the action
	 * and after() method after the action finishes.
	 *
	 * @param string $action Name of the action to execute.
	 * @return void
	 * @throws \Opake\Exception\PageNotFound If the specified action doesn't exist
	 */
	public function run($action)
	{
		$method = $this->getActionMethod($action);

		$this->execute = true;
		$this->before();
		if ($this->execute) {
			$this->$method();
		}
		if ($this->execute) {
			$this->after();
		}
	}

	/**
	 * @param string $role
	 * @return \Opake\Model\User|bool
	 */
	protected function logged($role = null)
	{
		if ($this->pixie->auth->user() == null)
			return false;

		if ($role && !$this->pixie->auth->has_role($role))
			return false;

		return $this->pixie->auth->user();
	}

	public function flash($messageId, $text)
	{
		$this->pixie->session->flash($messageId, $text);
	}

	protected function getActionMethod($action)
	{
		$method = 'action' . ucfirst($action);

		if (!method_exists($this, $method)) {
			throw new \Opake\Exception\PageNotFound();
		}
		return $method;
	}

	protected function loadModel($model, $key)
	{
		$item = $this->orm->get($model, $this->request->param($key));

		if (!$item->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}
		return $item;
	}

	protected function logSystemError($e)
	{
		if (!($e instanceof \Opake\Exception\HttpException || $e instanceof \Opake\Exception\ValidationError)) {
			$this->pixie->logger->exception($e);
		}
	}

}
