<?php


namespace Opake\Model\User;

use Opake\Helper\SessionHelper;
use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

abstract class AbstractSessionModel extends AbstractModel
{

	/**
	 * Update once per 5 minutes
	 * This value should be less than minimum time of a session
	 */
	const UPDATE_EXPIRED_TIME = 300;

	/**
	 * @return bool
	 */
	public function hasExpired()
	{
		return $this->expired !== null;
	}

	/**
	 * @return bool
	 */
	public function isPersistSession()
	{
		return $this->is_remember_me == 1;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function isSessionExpired()
	{
		$currentDate = new \DateTime();
		if ($this->expired === null) {
			throw new \Exception('Expired time is not set');
		}

		$expiredDate = TimeFormat::fromDBDatetime($this->expired);

		return (($currentDate->getTimestamp() - $expiredDate->getTimestamp()) >= AbstractSessionModel::UPDATE_EXPIRED_TIME);
	}

	public function getHash()
	{
		return $this->hash;
	}

	public function generateHash()
	{
		$this->hash = $this->generateRandomHash();

		if ($this->findUserSessionByHash($this->hash)) {
			$this->generateHash();
		}
	}

	public function compareHash($hash)
	{
		$this->hash = $hash;
	}

	public function setStarted($dateTime)
	{
		$this->started = TimeFormat::formatToDBDatetime($dateTime);
	}

	public function setExpired($dateTime)
	{
		$this->expired = TimeFormat::formatToDBDatetime($dateTime);
	}

	public function setRememberMe($rememberMe)
	{
		$this->is_remember_me = ($rememberMe) ? 1 : 0;
	}

	public function setActive($active)
	{
		$this->active = ($active) ? 1 : 0;
	}

	public function isActive()
	{
		return $this->active == 1;
	}

	public function updateExpiredTime($addSeconds)
	{
		$expiredTime = new \DateTime();
		$expiredTime->modify('+ ' . $addSeconds . ' seconds');
		$this->pixie->db->query('update')
			->table($this->table)
			->data([
				'expired' => TimeFormat::formatToDBDatetime($expiredTime)
			])
			->where('id', $this->id())
			->execute();
	}

	abstract public function setUserId($userId);

	abstract public function getUserId();

	abstract public function getUser();

	abstract public function disableAllOtherSessionsForUser($user);

	abstract public function findUserSessionByHash($hash);

	protected function generateRandomHash()
	{
		return SessionHelper::generateHash(42);
	}
}