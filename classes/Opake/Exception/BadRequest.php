<?php

namespace Opake\Exception;

class BadRequest extends HttpException
{

	protected $code = 400;
	protected $message = 'Bad Request';

	public function __construct($errors = null)
	{
		if (is_array($errors)) {
			$this->errors = $errors;
		} else {
			$this->suggestion = $errors;
		}
		
	}

	public function getJsonData()
	{
		$result = [];
		if (isset($this->suggestion)) {
			$result['suggestion'] = $this->suggestion;
		}
		if (isset($this->errors)) {
			$result['errors'] = $this->errors;
		}
		return json_encode($result);
	}

}
