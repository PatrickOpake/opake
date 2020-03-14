<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util;


class StringQueue
{
	protected $pointer;
	protected $message;

	public function __construct($s)
	{
		$s = rtrim($s, '~');
		$this->message = explode('~', $s);
		$this->pointer = 0;
	}

	public function size()
	{
		return count($this->message);
	}

	public function getNext()
	{
		return $this->message[$this->pointer++] . "~";
	}

	public function peekNext()
	{
		return $this->message[$this->pointer] . "~";
	}

	public function reset()
	{
		$this->pointer = 0;
	}

	public function hasNext()
	{
		return $this->pointer >= 0 && $this->pointer < count($this->message);
	}

}