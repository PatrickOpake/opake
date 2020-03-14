<?php

namespace OpakePatients\Model;

use Opake\Model\Organization as OpakeOrganization;

class Organization extends OpakeOrganization {

	public function toArray() {
		return [
			'id' => (int) $this->id,
			'name' => $this->name,
			'logo_src' => $this->getLogo('default'),
		];
	}

}
