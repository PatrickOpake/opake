<?php

namespace OpakeAdmin\Controller\Orders;

use OpakeAdmin\Controller\AuthPage;

class Internal extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->setBreadcrumbs(['/orders/internal/' => 'Purchase Orders']);
		$this->view->topMenuActive = 'orders';
	}

	public function actionIndex()
	{
		$this->view->subview = 'orders/internal/index';
	}

	public function actionView()
	{
		$service = $this->services->get('orders');
		$order = $service->getItem($this->request->param('id'));
		if (!$order->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}
		$this->view->order = $order;
		$this->view->subview = 'orders/internal/view';
	}

}
