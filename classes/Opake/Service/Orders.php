<?php

namespace Opake\Service;

use Opake\Helper\Pagination;

class Orders extends AbstractService
{

	protected $base_model = 'Order';

	/**
	 * Возвращает список заказов с указанием количества элементов
	 * @param Organization $org_id
	 * @param Pagination $pages
	 * @return array
	 */
	public function getItems($org_id, Pagination $pages)
	{
		$model = $this->getItem();
		$model->query
			->fields('order.*', $this->db->expr("count(order.id) as items"))
			->join(['order_item', 'oi'], ['order.id', 'oi.order_id'])
			->group_by('order.id');
		if ($org_id) {
			$model->query->having('organization_id', $org_id);
		}
		$model->pagination($pages);
		return $model->find_all();
	}

	public function getOrderItem($id)
	{
		return $this->orm->get('Order_Item')
			->where('id', $id)
			->find();
	}

}
