<?php

namespace Opake\Auth;

use Opake\Model\User\AbstractSessionModel;

class Service extends \PHPixie\Auth\Service
{
	/**
	 * @var AbstractSessionModel
	 */
	protected $currentSession;

	/**
	 * @param AbstractSessionModel $currentSession
	 */
	public function set_current_session($currentSession)
	{
		$this->currentSession = $currentSession;
	}

	/**
	 * @return AbstractSessionModel
	 */
	public function current_session()
	{
		return $this->currentSession;
	}
}