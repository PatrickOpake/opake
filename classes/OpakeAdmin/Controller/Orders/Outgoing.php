<?php

namespace OpakeAdmin\Controller\Orders;

class Outgoing extends Orders
{

	public function actionView()
	{
		$service = $this->services->get('orders_outgoing');
		$order = $service->getItem($this->request->param('subid'));
		if (!$order->loaded()) {
			throw new \Opake\Exception\PageNotFound();
		}
		$this->view->order = $order;
		$this->view->subview = 'orders/outgoing/view';
	}

	public function actionAdding()
	{
		$service = $this->services->get('orders_outgoing');
		$this->view->order = $service->getItem($this->request->param('subid'));
		$this->view->subview = 'orders/outgoing/adding';
	}

}
