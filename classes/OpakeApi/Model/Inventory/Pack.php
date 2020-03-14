<?php

namespace OpakeApi\Model\Inventory;

use Opake\Model\Inventory\Pack as OpakePack;
use OpakeApi\Model\Api;

class Pack extends OpakePack
{
	use Api;

	public function fromArray($data)
	{
		return $this->apiFill([
			'qty' => 'quantity',
			'expdate' => 'exp_date',
			'locationid' => 'location_id',
			'distributorid' => 'distributor_id'
		], $data);
	}

	public function toArray()
	{

		return [
			'itempackid' => (int)$this->id,
			'qty' => (int)$this->quantity,
			'expdate' => $this->exp_date,
			'locationid' => (int)$this->location->id,
			'locationname' => $this->location->name,
			'distributorid' => (int)$this->distributor_id,
		];
	}

}
