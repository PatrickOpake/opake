<?php

namespace OpakeApi\Exception;

class Forbidden extends HttpException
{
	protected $code = 403;
	protected $message = 'Forbidden';
}
