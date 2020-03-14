<?php

namespace Opake\Service\Orders;

class Outgoing extends \Opake\Service\AbstractService
{

	protected $base_model = 'Order_Outgoing';

	public function addItems(\Opake\Model\Order\Outgoing $order, $items)
	{
		foreach ($items as $item) {
			$orderItem = $this->orm->get('Order_Outgoing_Item');
			$orderItem->order_id = $order->id;
			$orderItem->inventory_id = $item->id;
			if ($item->min_level && $item->max_level) {
				$orderItem->count = ceil(($item->min_level + $item->max_level) / 2);
			} elseif ($item->min_level || $item->max_level) {
				$orderItem->count = $item->min_level ? $item->min_level : $item->max_level;
			}

			$vendor_id = 0;
			if ($item->manufacturer->loaded()) {
				$vendor_id = $item->manufacturer->id;
			} else {
				$supply = $item->supplies->find();
				if ($supply->loaded()) {
					$vendor_id = $supply->vendor_id;
				}
			}

			$orderGroupVendor = $this->orm->get('Order_Outgoing_Group_Vendor');
			if ($vendor_id) {
				$orderGroupVendor = $orderGroupVendor->where('order_id', $order->id)->where('vendor_id', $vendor_id)->find();
				if (!$orderGroupVendor->id) {
					$orderGroupVendor->order_id = $order->id;
					$orderGroupVendor->vendor_id = $vendor_id;
					$orderGroupVendor->save();
				}
			}

			if ($orderGroupVendor->id) {
				$orderItem->group_id = $orderGroupVendor->id;
				$orderItem->save();
			}
		}
	}

	public function getOrderItem($id)
	{
		return $this->orm->get('Order_Outgoing_Item')
			->where('id', $id)
			->find();
	}

	public function updateCount($model, $count)
	{
		$this->db->query('update')->table($model->table)
			->data(['count' => $count])
			->where('id', $model->id)
			->execute();
	}

}
