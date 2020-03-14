<?php

namespace Helper\Api;

class DataProvider
{
	/**
	 * @var \OpakeApi\Application
	 */
	protected $pixie;

	public function __construct($pixie)
	{
		$this->pixie = $pixie;
	}

	public function init()
	{
		$this->initUsers();
	}

	protected function initUsers()
	{
		$pixie = $this->pixie;

		$user = $pixie->orm->get('User');
		$user->where('email', 'api-test-admin@example.com');
		$user = $user->find();

		if (!$user->loaded()) {
			/** @var \OpakeApi\Model\User $newUser */
			$newUser = $pixie->orm->get('User');
			$newUser->email = 'api-test-admin@example.com';
			$newUser->role_id = 1;
			$newUser->status = 'active';
			$newUser->type = 'external';
			$newUser->first_name = 'ApiTest';
			$newUser->last_name = 'Admin';

			$newUser->time_create = \Opake\Helper\TimeFormat::formatToDBDatetime(new \DateTime());
			$newUser->phone = '123-123-123';
			$newUser->address = 'Unknown st. 41st apt.';
			$newUser->country_id = 235;
			$newUser->organization_id = 18;
			$newUser->profession_id = 5;
			$newUser->timezone = 293;

			$newUser->setPassword('password');

			$newUser->save();
		}

	}

}