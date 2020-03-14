<?php

namespace OpakePatients\Model\Cases;

class Item extends \Opake\Model\Cases\Item
{
	public function toArray()
	{
		$data = [
			'id' => $this->id(),
			'time_start' => date('D M d Y H:i:s O', strtotime($this->time_start)),
			'time_end' => date('D M d Y H:i:s O', strtotime($this->time_end)),
			'type' => $this->type->toArray(),
			'location' => $this->location->toArray(),
		];

		$surgeons = [];
		foreach ($this->users->find_all() as $user) {
			$surgeons[] = [
				'full_name' => $user->getFullName()
			];
		}

		$data['surgeons'] = $surgeons;

		return $data;
	}
}