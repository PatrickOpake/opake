<?php

namespace Opake\Service\Clients;

class Sites extends \Opake\Service\AbstractService
{

	protected $base_model = 'Site';

	public function preFill($site, $org)
	{
		$site->organization_id = $org->id;

		$site->address = $org->address;
		$site->country_id = $org->country_id;
		$site->website = $org->website;
		$site->contact_name = $org->contact_name;
		$site->contact_email = $org->contact_email;
		$site->contact_phone = $org->contact_phone;
	}

}
