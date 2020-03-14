<?php

namespace Opake\Exception;

class HttpException extends \Opake\Exception\AbstractException
{
	protected $code = 500;
	protected $message = 'Internal Server Error';
}