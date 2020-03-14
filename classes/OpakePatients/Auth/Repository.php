<?php

namespace OpakePatients\Auth;

use OpakePatients\Exception\Authentication;

class Repository extends \Opake\Auth\Repository
{

	public function get_by_login($login)
	{
		$login_field = $this->login_field;
		$user = $this->service->user_model()->with('patient')->where($login_field, $login)->find();
		if (!$user->loaded()) {
			throw Authentication::userNotFound();
		}
		return $user;
	}

	public function get_by_id($id)
	{
		$user = $this->service->user_model()
			->with('patient')
			->where('id', $id)
			->find();

		return $user;
	}

}
