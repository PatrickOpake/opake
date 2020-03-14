<?php

namespace OpakeAdmin\Controller\Inventory;

use OpakeAdmin\Controller\AuthPage;

class Internal extends AuthPage
{

	public function before()
	{
		parent::before();

		if (!$this->logged()->isInternal()) {
			throw new \Opake\Exception\Forbidden();
		}

		$this->view->setBreadcrumbs(['/inventory/internal/' => 'Master Inventory']);
		$this->view->topMenuActive = 'inventory';
	}

	public function actionIndex()
	{
		$service = $this->services->get('inventory');
		$model = $service->getItem();

		$search = new \OpakeAdmin\Model\Search\Inventory($this->pixie);
		$results = $search->search($model, $this->request);

		$this->view->list = $results;
		$this->view->pages = $search->getPagination();
		$this->view->filters = $search->getParams();
		$this->view->subview = 'inventory/internal/index';
	}

}
