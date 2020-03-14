<?php

namespace OpakeApi\Model;

use Opake\Model\Organization as OpakeOrganization;

class Organization extends OpakeOrganization
{

	public function toArray()
	{
		$orgPermissions = new \Opake\Permissions\Organization\OrganizationLevel($this);

		return [
			'organizationid' => (int)$this->id,
			'organizationname' => $this->name,
			'organizationdetails' => [
				'name' => $this->name,
				'address' => $this->address,
				'country' => $this->country->name,
				'website' => $this->website,
			],
			'administratorinfo' => [
				'name' => $this->contact_name,
				'phone' => $this->contact_phone,
				'email' => $this->contact_email,
			],
			'permissions' => [
				'settings' => $orgPermissions->getOrganizationPermissions(),
			],
			'nuanceorgid' => $this->nuance_org_id
		];
	}

}
