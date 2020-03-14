<?php

namespace OpakeApi\Exception;

class BadRequest extends HttpException
{

	protected $code = 400;
	protected $message = 'Bad Request';
	protected $suggestion = '';

	public function __construct($suggestion)
	{
		$this->suggestion = $suggestion;
		parent::__construct();
	}

	public function getStatus()
	{
		$result = array(
			'code' => $this->getCode(),
			'message' => $this->getMessage(),
		);
		if ($this->suggestion) {
			$result['suggestion'] = $this->suggestion;
		}
		return $result;
	}

}
