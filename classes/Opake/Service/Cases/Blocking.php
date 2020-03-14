<?php

namespace Opake\Service\Cases;


use Opake\Model\Cases\Setting;

class Blocking extends \Opake\Service\AbstractService
{

	protected $base_model = 'Cases_Blocking';

	public function cleanExpired($block_item, $org)
	{
		$setting_service = $this->pixie->services->get('cases_settings');
		$setting = $setting_service->getSetting($org);
		if ($setting) {
			$block_hour = $setting->getTimingHour();
		} else {
			$block_hour = Setting::getDefaultBlockHour();
		}

		if ($block_hour) {
			$endTime = new \DateTime($block_item->end);
			$endTime->add(new \DateInterval('PT' . $block_hour . 'H'));
			if ($endTime <= new \DateTime("now")) {
				$block_item->delete();
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

}
