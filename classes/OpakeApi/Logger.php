<?php

namespace OpakeApi;

class Logger extends \Opake\Logger
{
	protected function getLogFileName()
	{
		return 'api.log';
	}
}
