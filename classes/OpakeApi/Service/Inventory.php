<?php

namespace OpakeApi\Service;

class Inventory extends \Opake\Service\Inventory\Inventory
{

	public function updatePacks($model, $packs_data)
	{
		$packs = [];
		foreach ($model->packs->find_all() as $pack) {
			$packs[$pack->id] = $pack;
		}

		foreach ($packs_data as $data) {
			if (isset($data->itempackid)) {
				if (!isset($packs[$data->itempackid])) {
					throw new \Exception('Unknown itempack ' . $data->itempackid);
				} elseif ($packs[$data->itempackid]->inventory_id != $model->id) {
					throw new \Exception('Wrong itempack ' . $data->itempackid);
				}
			}
		}

		foreach ($packs_data as $data) {
			if (isset($data->itempackid)) {
				$pack = $packs[$data->itempackid];
				$pack->fill($data);
				unset($packs[$pack->id]);
			} else {
				$pack = $this->orm->get('Inventory_Pack');
				$pack->inventory_id = $model->id;
				$pack->fill($data);
			}
			$pack->save();
		}

		foreach ($packs as $pack) {
			$pack->delete();
		}
	}

	public function moveItems($data)
	{
		$ids = [];
		foreach ($data as $item) {
			$pack = $this->orm->get('Inventory_Pack', $item->itempackid);
			$location = $this->orm->get('Location_Storage', $item->newlocationid);

			if (!$pack->loaded() || $pack->inventory->organization_id !== $this->getUser()->organization_id) {
				throw new \Exception('Unknown itempack ' . $item->itempackid);
			}
			if (!$location->loaded() || $location->site->organization_id !== $this->getUser()->organization_id) {
				throw new \Exception('Unknown location ' . $item->newlocationid);
			}
			if (!isset($item->qty)) {
				throw new \Exception('Quantity not specified');
			}
			if ($item->qty > $pack->quantity) {
				throw new \Exception('Not enough elements in pack ' . $pack->id);
			}
			if ($location->id == $pack->location_id) {
				throw new \Exception('Change location');
			}

			$isNewPack = true;
			foreach ($location->packs->find_all() as $locpk) {
				if ($locpk->inventory_id == $pack->inventory_id && $locpk->exp_date == $pack->exp_date) {
					$locpk->quantity += $item->qty;
					$locpk->save();
					$ids[] = $locpk->id;
					$isNewPack = false;
				}
			}

			if ($isNewPack) {
				$newPack = $this->orm->get('Inventory_Pack');
				$newPack->inventory_id = $pack->inventory_id;
				$newPack->location_id = $location->id;
				$newPack->distributor_id = $pack->distributor_id;
				$newPack->order_item_id = $pack->order_item_id;
				$newPack->exp_date = $pack->exp_date;
				$newPack->quantity = $item->qty;
				$newPack->save();
				$ids[] = $newPack->id;
			}

			$pack->quantity -= $item->qty;
			if ($pack->quantity > 0) {
				$pack->save();
			} else {
				$pack->delete();
			}
		}
		return $ids;
	}

	/**
	 * Поиск инвентаря
	 * @param String $query
	 * @return array
	 */
	public function searchApi($query, $org_id, $offset, $limit)
	{
		$model = $this->getItem();
		$model->where('organization_id', $org_id);
		//$model->where('status', \Opake\Model\Inventory::STATUS_ACTIVE);

		if ($query) {
			$model->where('name', 'like', '%' . $query . '%');
		}
		if ($offset) {
			$model->offset($offset);
		}
		if ($limit) {
			$model->limit($limit);
		}
		$model->order_by('name', 'asc');
		return $model->find_all();
	}

}
