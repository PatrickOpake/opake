<?php

namespace OpakeApi\Model;

use Opake\Model\Location as OpakeLocation;

class Location extends OpakeLocation
{

	public function toArray()
	{
		return [
			'storageid' => (int)$this->id,
			'storagename' => $this->name
		];
	}

}
