<?php

namespace OpakeApi\Service;

class Orders extends \Opake\Service\Orders
{

	protected $_recived = [];

	public function saveItems($model, $packs_data)
	{
		$items = [];
		foreach ($packs_data as $data) {
			$inventory = $this->orm->get('Inventory', $data->id);
			if (!$inventory->loaded()) {
				throw new \Exception('Unknown Item ' . $data->id);
			}
			$this->_recived[] = $inventory;

			if (!$this->orm->get('Location', $data->locationid)->loaded()) {
				throw new \Exception('Unknown Location ' . $data->locationid);
			}

			$item = $this->orm->get('Order_Item');
			$item->order_id = $model->id;
			$item->fill($data);

			$item->save();
			$items[] = $item;
		}

		if ($items) {
			foreach ($items as $item) {
				$pack = $this->orm->get('Inventory_Pack');
				$pack->inventory_id = $item->inventory_id;
				$pack->location_id = $item->location_id;
				$pack->distributor_id = $model->vendor_id;
				$pack->order_item_id = $item->id;
				$pack->exp_date = $item->exp_date;
				$pack->quantity = $item->received;
				$pack->save();
			}
		}
	}

	public function postProcessing()
	{
		foreach ($this->_recived as $inventory) {
			$this->pixie->events->fireEvent('save.packs', $inventory);
		}
	}
}
