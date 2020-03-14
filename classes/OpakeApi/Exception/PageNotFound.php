<?php

namespace OpakeApi\Exception;

class PageNotFound extends HttpException
{
	protected $code = 404;
	protected $message = 'Not Found';
}
