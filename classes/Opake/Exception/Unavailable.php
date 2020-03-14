<?php

namespace Opake\Exception;

class Unavailable extends HttpException
{
	protected $code = 503;
	protected $message = 'Service Temporarily Unavailable';
}
