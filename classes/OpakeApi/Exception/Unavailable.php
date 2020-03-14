<?php

namespace OpakeApi\Exception;

class Unavailable extends HttpException
{
	protected $code = 503;
	protected $message = 'Service Temporarily Unavailable';


	public function getJsonMessage()
	{
		$httpStatus = sprintf("%d %s", $this->getCode(), $this->getMessage());
		header($_SERVER["SERVER_PROTOCOL"] . ' ' . $httpStatus);
		header("Status: " . $httpStatus);

		return parent::getJsonMessage();
	}
}
