<?php

namespace OpakeAdmin\Controller\Orders\Ajax;

use OpakeAdmin\Model\Search\Order\Received as OrderSearch;

class Internal extends \OpakeAdmin\Controller\Ajax
{
	public function actionIndex()
	{
		$items = [];
		$model = $this->orm->get('Order');

		$search = new OrderSearch($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}
}
