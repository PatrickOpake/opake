<?php

namespace OpakeAdmin\Controller\Vendors\Internal;

use Opake\Model\Vendor;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function actionList()
	{
		$service = $this->services->get('vendors');
		$model = $service->getItem();

		$search = new \OpakeAdmin\Model\Search\Vendor($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $item) {
			$items[] = $item->toShortArray();
		}
		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}
}
