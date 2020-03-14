<?php

namespace OpakeApi\Controller;

abstract class AbstractController extends \Opake\Controller\AbstractController
{

	/**
	 * Result Array for API response
	 * @var array
	 */
	protected $result = NULL;

	/**
	 * Result message
	 * @var string
	 */
	protected $message = NULL;

	protected function getActionMethod($action)
	{
		$method = parent::getActionMethod($action);

		if (!$this->logged() && $action !== 'login' && $action !== 'resetpwd' && $action !== 'anon') {
			throw new \OpakeApi\Exception\Unauthorized();
		}
		return $method;
	}

	public function after()
	{
		$result = array('status' =>
			array(
				'code' => 0,
				'message' => $this->message,
			),
			'data' => $this->result
		);
		$this->response->body = json_encode($result, JSON_PRETTY_PRINT);
	}

	/**
	 * Выгружает модель из запроса
	 * @param String $model
	 * @param String $key
	 * @return \Opake\Model\AbstractOrm
	 * @throws \OpakeApi\Exception\BadRequest
	 * @throws \OpakeApi\Exception\PageNotFound
	 */
	protected function loadModel($model, $key, $type = 'get')
	{
		$modelId = $this->request->$type($key);

		if (!$modelId) {
			throw new \OpakeApi\Exception\BadRequest("'$key' expected");
		}

		$item = $this->orm->get($model, $modelId);

		if (!$item->loaded()) {
			throw new \OpakeApi\Exception\PageNotFound();
		}
		return $item;
	}

	protected function checkAccess($section, $action, $model = null)
	{
		if ($access = $this->pixie->permissions->checkAccess($section, $action, $model)) {
			return $access;
		}
		throw new \Opake\Exception\Forbidden();
	}

	protected function logSystemError($e)
	{
		if (!($e instanceof \Opake\Exception\HttpException || $e instanceof \Opake\Exception\ValidationError ||
			$e instanceof \OpakeApi\Exception\HttpException)) {
			$this->pixie->logger->exception($e);
		}
	}

}
