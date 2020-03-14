<?php

namespace OpakeApi\Exception;

class Unauthorized extends HttpException
{
	protected $code = 401;
	protected $message = 'Unauthorized';
}
