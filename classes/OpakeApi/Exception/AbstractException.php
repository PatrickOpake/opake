<?php

namespace OpakeApi\Exception;

abstract class AbstractException extends \Exception
{

	public function getJsonMessage()
	{
		$message = array(
			'status' => $this->getStatus(),
			'data' => NULL,
		);

		return json_encode($message);
	}

	public function getStatus()
	{
		return array(
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
		);
	}

}
