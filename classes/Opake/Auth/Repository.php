<?php

namespace Opake\Auth;

class Repository extends \PHPixie\Auth\Repository\ORM
{

	public function get_by_login($login)
	{
		//login by email - backward compatibility
		$user = $this->service->user_model()
			->where('and', [
				['username', $login],
				['or', [
						['username', 'IS', $this->pixie->db->expr('NULL')],
						['email', $login],
					],
				]
			])
			->find();

		return $user;
	}

	public function get_by_id($id)
	{
		$user = $this->service->user_model()
			->where('id', $id)
			->find();

		return $user;
	}

}
