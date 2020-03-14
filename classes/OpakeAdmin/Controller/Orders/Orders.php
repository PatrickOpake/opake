<?php

namespace OpakeAdmin\Controller\Orders;

use OpakeAdmin\Controller\AuthPage;

class Orders extends AuthPage
{

	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));

		$this->view->addBreadCrumbs(array('/orders/' . $this->org->id => 'Purchase Orders'));
		$this->view->setActiveMenu('inventory.orders');
		$this->view->set_template('inner');
	}

	public function actionIndex()
	{
		$service = $this->services->get('orders');
		$model = $service->getItem()->where('organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Order\Received($this->pixie);
		$results = $search->search($model, $this->request);

		$this->view->list = $results;
		$this->view->pages = $search->getPagination();
		$this->view->filters = $search->getParams();
		$this->view->subview = 'orders/index';
	}

	public function actionView()
	{
		$service = $this->services->get('orders');
		$order = $service->getItem($this->request->param('subid'));
		if (!$order->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}
		$this->view->order = $order;
		$this->view->subview = 'orders/received/view';
	}

	public function actionAdding()
	{
		$service = $this->services->get('orders');
		$this->view->order = $service->getItem();
		$this->view->subview = 'orders/received/adding';
	}

}
