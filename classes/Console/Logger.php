<?php

namespace Console;

class Logger extends \Opake\Logger
{
	protected function getLogFileName()
	{
		return 'console.log';
	}
}
