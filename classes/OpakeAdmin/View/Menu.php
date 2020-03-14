<?php

namespace OpakeAdmin\View;

class Menu
{
	protected $activeMenu;
	protected $menuConfig;
	protected $menuType;

	/**
	 * @param string|null $menuType
	 */
	public function __construct($menuType = null)
	{
		$app = \Opake\Application::get();
		if ($menuType === 'settings') {
			$this->menuConfig = $app->config->get('menu-settings');
		} else {
			$this->menuConfig = $app->config->get('menu');
		}
		$this->menuType = $menuType;
	}

	public function getFullMenuConfig()
	{
		return $this->menuConfig;
	}

	public function setFullMenuConfig($menuConfig)
	{
		$this->menuConfig = $menuConfig;
	}

	public function getMenuType()
	{
		return $this->menuType;
	}

	public function getActiveMenu($depth = 0)
	{
		if ($this->activeMenu) {
			return isset($this->activeMenu[$depth]) ? $this->activeMenu[$depth] : null;
		}
		return null;
	}

	public function setActiveMenu($item)
	{
		$this->activeMenu = explode('.', $item);
	}

	public function getMenuConfig($depth = 0)
	{
		$menu = $this->menuConfig;
		for ($i = 0; $i < $depth; $i++) {
			$active = $this->getActiveMenu($i);
			$menu = isset($menu[$active]['items']) ? $menu[$active]['items'] : [];
		}
		return $menu;
	}
}