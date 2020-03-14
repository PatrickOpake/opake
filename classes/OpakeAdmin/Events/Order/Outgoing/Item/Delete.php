<?php

namespace OpakeAdmin\Events\Order\Outgoing\Item;

class Delete extends \Opake\Events\AbstractListener {


	/**
	 * Отслеживает изменения в исходящем заказе
	 *
	 * @param \Opake\Model\Order\Outgoing\Item $item
	 */
	public function dispatch($item) {
		$group = $item->group;
		if($group->items->count_all() <= 1) {
			$group->delete();
		}
	}


}
