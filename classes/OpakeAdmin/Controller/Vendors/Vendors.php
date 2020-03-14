<?php

namespace OpakeAdmin\Controller\Vendors;

use Opake\Helper\Pagination;

class Vendors extends \OpakeAdmin\Controller\AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(['/vendors/' . $this->org->id => 'Vendors']);
		$this->view->setActiveMenu('settings.databases.vendors');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$this->checkAccess('vendors', 'view');
		$this->view->subview = 'vendors/index';
	}

	public function actionView()
	{
		$this->checkAccess('vendors', 'view');

		$service = $this->services->get('vendors');

		$id = $this->request->param('subid');
		$vendor = $service->getItem($id);

		if (!$vendor->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$this->view->vendor = $vendor;
		$this->view->addBreadCrumbs([
			'/vendors/' . $this->org->id . '/view/' . $id => $vendor->name
		]);
		$this->view->leftMenu = 'vendors/_left-menu';
		$this->view->leftMenuPhoto = $vendor->getLogo('default');
		$this->view->leftMenuActive = 'profile';
		$this->view->subview = 'vendors/view';
	}

	public function actionCreate()
	{
		$this->view->subview = 'vendors/create';
	}

	public function actionProducts()
	{
		$this->checkAccess('vendors', 'view');

		$service = $this->services->get('vendors');

		$id = $this->request->param('subid');
		$vendor = $service->getItem($id);

		if (!$vendor->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}

		$products = $service->getProducts($vendor);
		$pages = new Pagination($products->count_all(), $this->request->get('p'), $this->request->get('l'));

		if ($vendor->is_manf || $vendor->is_dist) {
			$list = $products->pagination($pages)->find_all()->as_array();
		} else {
			$list = [];
		}

		$this->view->vendor = $vendor;
		$this->view->list = $list;
		$this->view->pages = $pages;

		$this->view->addBreadCrumbs([
			'/vendors/' . $this->org->id . '/edit/' . $id => $vendor->name,
			'/vendors/' . $this->org->id . '/products/' . $id => 'Products'
		]);
		$this->view->leftMenu = 'vendors/_left-menu';
		$this->view->leftMenuActive = 'products';
		$this->view->subview = 'vendors/products';
	}
}
