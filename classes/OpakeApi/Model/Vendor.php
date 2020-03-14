<?php

namespace OpakeApi\Model;

use Opake\Model\Vendor as OpakeVendor;

class Vendor extends OpakeVendor
{

	public function toArray($detailed = true)
	{
		if ($detailed) {
			return [
				'vendorid' => (int)$this->id,
				'name' => $this->name,
				'contactname' => $this->contact_name,
				'contactphone' => $this->contact_phone,
				'contactemail' => $this->contact_email,
				'website' => $this->website,
				'address' => $this->address,
				'country' => $this->country->name,
				'phone' => $this->phone,
				'email' => $this->email,
				'other' => '',
				'mmi' => $this->mmis_id,
			];
		} else {
			return [
				'vendorid' => (int)$this->id,
				'name' => $this->name,
				'email' => $this->email
			];
		}
	}

}
