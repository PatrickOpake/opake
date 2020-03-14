<?php

namespace Opake\Service\Clients;

class Users extends \Opake\Service\AbstractService
{

	protected $base_model = 'User';

	public function getRoles()
	{
		return $this->orm->get('role')->order_by('id', 'asc')->find_all();
	}

	public function getProfessions()
	{
		return $this->orm->get('profession')->order_by('id', 'asc')->find_all();
	}

}
