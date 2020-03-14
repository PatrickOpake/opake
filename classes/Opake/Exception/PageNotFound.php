<?php

namespace Opake\Exception;

class PageNotFound extends HttpException
{

	protected $code = 404;
	protected $message = 'Page Not Found';

}
