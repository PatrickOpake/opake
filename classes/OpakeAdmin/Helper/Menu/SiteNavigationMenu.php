<?php

namespace OpakeAdmin\Helper\Menu;

use Opake\Model\Role;

class SiteNavigationMenu
{
	public static function prepareMenu($view, $siteId)
	{
		$config = $view->getMenu()->getFullMenuConfig();

		$sitesItems = [
			'profile' => [
				'title' => 'Profile',
				'url' => '/clients/sites/%s/view/' . $siteId,
				'access' => [Role::FullAdmin, Role::Biller],
			],
			'fee-schedule' => [
				'title' => 'Fee Schedule',
				'url' => '/clients/sites/%s/fee-schedule/view/' . $siteId,
				'access' => [Role::FullAdmin, Role::Biller],
			],
			'charges' => [
				'title' => 'Charge Master',
				'url' => '/clients/sites/%s/charges-master/view/' . $siteId,
				'access' => [Role::FullAdmin, Role::Biller]
			]
		];

		$config['settings']['items']['organization']['items']['sites']['items'] = $sitesItems;


		$view->getMenu()->setFullMenuConfig($config);
	}
}