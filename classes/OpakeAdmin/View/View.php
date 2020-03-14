<?php

namespace OpakeAdmin\View;

use Opake\Helper\Config;
use Opake\Extentions\Less\Compiler;
use OpakeAdmin\Controller\Clients\Users\Ajax\Password;

class View extends \Opake\View\View
{
	protected $menu;
	protected $org;

	public $breadcrumbs = null;
	public $wrapContent = true;
	public $showSideCalendar = false;
	public $showScheduleLegend = false;

	public function __construct($pixie, $helper, $name)
	{
		parent::__construct($pixie, $helper, $name);
		$this->breadcrumbs = ['/' => 'Home'];
	}

	// Breadcrumbs
	public function getBreadcrumbs()
	{
		$user = $this->pixie->auth->user();
		if (empty($this->breadcrumbs) || !$user || !$user->isInternal()) {
			return '';
		}
		$last = sizeof($this->breadcrumbs);
		$cnt = 0;
		$str = '<div class="site-breadcrumbs"><div class="container-fluid"><div class="row"><ol class="breadcrumb">';
		foreach ($this->breadcrumbs as $link => $text) {
			$cnt++;
			$str .= sprintf('<li class="%s"><a href="%s">%s</a></li>', $cnt == $last ? 'active' : '', $link, $text);
		}
		$str .= '</ol></div></div></div>';
		return $str;
	}

	public function setBreadcrumbs($breadcrumbs = array())
	{
		$this->breadcrumbs = $breadcrumbs;
	}

	public function addBreadCrumbs($item = array())
	{
		foreach ($item as $link => $text) {
			$this->breadcrumbs[$link] = $text;
		}
	}

	public function getActiveMenu($depth = 0)
	{
		return $this->getMenu()->getActiveMenu($depth);
	}

	public function setActiveMenu($item)
	{
		$this->getMenu()->setActiveMenu($item);
	}

	public function getMenuConfig($depth = 0)
	{
		return $this->getMenu()->getMenuConfig($depth);
	}

	public function getJsMenuConfig($org, $depth = 0)
	{
		$result = [];
		$active = $this->getActiveMenu();
		foreach ($this->getMenuConfig($depth) as $key => $item) {
			if (isset($item['title'])) {
				if ($this->helper->menu_check_access($item, $this->pixie->auth->user(), $org)) {
					if(empty($org)) {
						$firstItemLink = $this->helper->menu_get_first_has_access($item, $this->pixie->auth->user());
					} else {
						$firstItemLink = sprintf($this->helper->menu_get_first_has_access($item, $this->pixie->auth->user(), $org), $org->id);
					}
					if ($firstItemLink !== '#') {
						if($active == $key) {
							$item['active'] = true;
						}
						$item['firstItemLink'] = $firstItemLink;
						$childs = [];
						$activeChild = $this->getActiveMenu(1);
						if(!empty($item['items'])) {
							foreach ($item['items'] as $keyChild => $itemChild) {
								if ($this->helper->menu_check_access($itemChild, $this->pixie->auth->user(), $org)) {
									if (isset($itemChild['url'])) {
										if(!empty($org)) {
											$itemChild['url'] = sprintf($itemChild['url'], $org->id);
										}
									} else {
										if(empty($org)) {
											$itemChild['url'] = $this->helper->menu_get_first_has_access($itemChild, $this->pixie->auth->user());
										} else {
											$itemChild['url'] = sprintf($this->helper->menu_get_first_has_access($itemChild, $this->pixie->auth->user(), $org), $org->id);
										}
									}
									if ($activeChild == $keyChild) {
										$itemChild['active'] = true;
									}
									$childs[$keyChild] = $itemChild;
								}
							}
						}
						$item['items'] = $childs;
						$result[$key] = $item;

					}
				}
			}
		}
		return $result;
	}

	/**
	 * @return Menu
	 */
	public function getMenu()
	{
		if (!$this->menu) {
			$this->initMenu();
		}

		return $this->menu;
	}

	public function initMenu($menuType = null)
	{
		$this->menu = new Menu($menuType);
	}

	public function setDefaultJsCss()
	{
		parent::setDefaultJsCss();

		$less = new Compiler($this->pixie, [
			$this->pixie->root_dir . 'apps/common/public/vendors/bootstrap/less/' => 'bootstrap',
			$this->pixie->root_dir . 'apps/common/assets/less/' => 'common'
		]);
		$less->force_compile = $this->forceCompileAndMinify;
		$less->compileFile('/assets/less/site.less', 'site.css');

		$this->addCSSList([
			// Vendors
			'/vendors/lightbox/css/lightbox.css',
			'/vendors/angular/angular-xeditable-0.1.8/css/xeditable.css',
			'/vendors/fullcalendar/fullcalendar.min.css',
			'/vendors/calculator-master/jquery.calculator.alt.css',

			// Compiled less
			'/css/site.css',
		]);

		$this->addJsList([
			// Vendors
			'/vendors/lightbox/js/lightbox.min.js' => true,
			'/vendors/angular/angular-xeditable-0.1.8/js/xeditable.min.js' => true,
			'/vendors/angular/ui-calendar-master/src/calendar.js' => true,
			'/vendors/angular/angular-drag-and-drop-lists-master/angular-drag-and-drop-lists.min.js' => true,
			'/vendors/angular/ng-file-upload-master/dist/ng-file-upload.min.js' => true,
			'/vendors/angular/moveable-modal.js' => true,
			'/vendors/angular/ui-tinymce-master/dist/tinymce.min.js' => false,
			'/vendors/calculator-master/jquery.plugin.js' => false,
			'/vendors/calculator-master/jquery.calculator.js' => false,

			'/js/index.js' => false,
		]);

		$this->addJSFromFolder('/js/app', false);
	}

}
