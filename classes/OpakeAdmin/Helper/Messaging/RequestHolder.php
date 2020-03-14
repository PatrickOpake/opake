<?php

namespace OpakeAdmin\Helper\Messaging;

class RequestHolder
{
	const DEFAULT_ITERATIONS = 20;

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var \Opake\Model\User
	 */
	protected $user;

	/**
	 * @var MessagingHelper
	 */
	protected $messagingHelper;

	/**
	 * @var int
	 */
	protected $timestamp;

	/**
	 * @var int
	 */
	protected $iterations;

	/**
	 * @var array
	 */
	public $messages;

	/**
	 * @param \Opake\Application $pixie
	 * @param \Opake\Model\User $user
	 * @param int $timestamp
	 * @param int $iterations
	 */
	public function __construct($pixie, $user, $timestamp, $iterations)
	{
		$this->pixie = $pixie;
		$this->user = $user;
		$this->timestamp = $timestamp;
		if ($iterations < 1 || $iterations > 60) {
			$iterations = self::DEFAULT_ITERATIONS;
		}
		$this->iterations = $iterations;
	}

	public function run()
	{
		$this->messagingHelper = new MessagingHelper($this->pixie, $this->user);
		for ($i = 0; $i < $this->iterations; $i++) {
			if ($this->timestamp === time()) {
				sleep(1);
			}
			$t = time();
			if ($this->checkUpdates()) {
				$this->timestamp = $t;
				break;
			} else {
				sleep(1);
			}
		}
	}

	/**
	 * @return int
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/**
	 * @return bool
	 */
	protected function checkUpdates()
	{
		$this->messages = $this->messagingHelper->getLastMessages($this->timestamp);
		if (!empty($this->messages)) {
			return true;
		}
		return false;
	}

}
