<?php

namespace Opake\Exception;

class FileNotFound extends HttpException
{

	protected $code = 404;
	protected $message = 'File Not Found';

}
