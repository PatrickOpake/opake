<?php

namespace Opake\Permissions;

class AccessLevel
{
	/**
	 * @var mixed
	 */
	protected $level;

	/**
	 * @param mixed $level
	 */
	public function  __construct($level)
	{
		$this->level = $level;
	}

	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->level === true;
	}

	/**
	 * @return bool
	 */
	public function isDisallowed()
	{
		return $this->level === false;
	}

	/**
	 * @return bool
	 */
	public function isSelfAllowed()
	{
		return $this->level === 'self';
	}

}