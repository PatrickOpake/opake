<?php

namespace OpakeApi\Controller;

class Orders extends AbstractController
{

	public function actionShippingtypes()
	{
		$items = [];
		foreach ($this->services->get('settings')->getList('Order_ShippingType') as $type) {
			$items[] = [
				'name' => $type->name
			];
		}
		$this->result = ['types' => $items];
	}

}
