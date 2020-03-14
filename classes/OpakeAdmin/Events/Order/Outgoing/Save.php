<?php

namespace OpakeAdmin\Events\Order\Outgoing;

class Save extends \Opake\Events\AbstractListener
{


	/**
	 * Отслеживает изменения в исходящем заказе
	 * 
	 * @param \Opake\Model\Order\Outgoing $order
	 */
	public function dispatch($outgoing_order) {
		if(!empty($outgoing_order->date)) {
			foreach($outgoing_order->groups->find_all() as $group) {
				$received_order = $this->orm->get('order');
				$received_order->organization_id = $outgoing_order->organization_id;
				$received_order->date = $outgoing_order->date;
				$received_order->vendor_id = $group->vendor_id;
				$received_order->save();

				if($received_order->id) {
					$group->received_order_id = $received_order->id;
					$group->save();
					foreach($group->items->find_all() as $item) {
						$received_order_item = $this->orm->get('order_item');
						$received_order_item->order_id = $received_order->id;
						$received_order_item->inventory_id = $item->inventory_id;
						$received_order_item->location_id = 0;
						$received_order_item->received = 0;
						$received_order_item->ordered = !empty($item->count) ? $item->count : 0;
						$received_order_item->save();
					}
				}
			}

		}
	}

}
