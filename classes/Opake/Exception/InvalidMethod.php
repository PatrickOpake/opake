<?php

namespace Opake\Exception;

class InvalidMethod extends HttpException
{

	protected $code = 405;
	protected $message = 'Invalid method';

}
