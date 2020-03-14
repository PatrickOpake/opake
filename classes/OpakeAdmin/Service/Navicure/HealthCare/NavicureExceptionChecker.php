<?php

namespace OpakeAdmin\Service\Navicure\HealthCare;

class NavicureExceptionChecker
{
	const STATUS_CODE_ACCOUNT_NOT_FOUND = 1150;

	protected $statusCode;

	protected static $status_code_msg = [
		self::STATUS_CODE_ACCOUNT_NOT_FOUND => 'Invalid Submitter ID or Submitter Pass',
	];

	public function __construct($code)
	{
		$this->statusCode = $code;
	}

	public function getStatusMessage()
	{
		if(isset(self::$status_code_msg[$this->statusCode])) {
			return self::$status_code_msg[$this->statusCode];
		}

		return '';
	}

}