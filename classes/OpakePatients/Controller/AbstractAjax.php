<?php

namespace OpakePatients\Controller;

use Opake\Controller\AbstractController;

class AbstractAjax extends AbstractController
{
	/**
	 * Result Array for response
	 * @var array
	 */
	protected $result = NULL;

	public function after()
	{
		$this->response->headers = [
			'Content-type: text/javascript;charset=UTF-8'
		];
		$this->response->body = json_encode($this->result);
	}

	/**
	 * Возвращает данные запроса
	 * @return object|array
	 */
	public function getData($asArray = false)
	{
		return json_decode($this->request->post('data', null, false), $asArray);
	}

}
