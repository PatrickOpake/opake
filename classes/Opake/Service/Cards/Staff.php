<?php

namespace Opake\Service\Cards;

class Staff extends \Opake\Service\Cards
{

	protected $base_model = 'Card_Staff';

	/**
	 * @param $caseid
	 * @param $userid
	 * @return mixed
	 */
	public function getCard($caseid, $userid)
	{
		return $this->getItem()->where('user_id', $userid)
			->where('case_id', $caseid)
			->find();
	}

}
