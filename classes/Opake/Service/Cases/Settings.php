<?php

namespace Opake\Service\Cases;

use Opake\Model\Cases\Setting;

class Settings extends \Opake\Service\AbstractService
{

	protected $base_model = 'Cases_Setting';

	public function getSetting($org_id = null)
	{
		if ($org_id) {
			$setting = $this->pixie->orm->get('Cases_Setting')->where('organization_id', $org_id)->find();
			if ($setting->loaded()) {
				return $setting;
			}
		}

		return null;
	}

}
