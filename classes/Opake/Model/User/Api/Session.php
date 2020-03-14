<?php

namespace Opake\Model\User\Api;

class Session extends \Opake\Model\User\Session
{
	protected function getCurrentLoggedInterface()
	{
		return \Opake\Model\User\Session::LOGGED_VIA_API;
	}

}