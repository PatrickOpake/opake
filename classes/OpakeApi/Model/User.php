<?php

namespace OpakeApi\Model;

use Opake\Helper\Config;
use Opake\Model\User as OpakeUser;

class User extends OpakeUser
{

	public function getPhoto($size = NULL)
	{
		$path = parent::getPhoto($size);
		return Config::get('app.web') . $path;
	}

	public function toArray($detailed = true)
	{
		if ($detailed) {
			return [
				'userid' => (int)$this->id,
				'accesslevel' => (int)$this->role->id,
				'email' => $this->getEmail(),
				'firstname' => $this->getFirstName(),
				'jobposition' => $this->profession->name,
				'lastname' => $this->getLastName(),
				'organization' => [
					'organizationid' => (int)$this->organization->id,
					'organizationname' => $this->organization->name,
					'nuanceorgid' => $this->organization->nuance_org_id
				],
				'phone' => $this->phone,
				'userphotourl' => $this->getPhoto(),
				'is_dictation_enabled' => (bool) $this->is_dictation_enabled,
			    'is_op_report_enabled' => (bool) $this->is_enabled_op_report
			];
		} else {
			return [
				'userid' => (int)$this->id,
				'fullname' => $this->getFullName(),
				'jobposition' => $this->profession->name,
			];
		}
	}

}
