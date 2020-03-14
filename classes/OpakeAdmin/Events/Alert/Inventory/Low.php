<?php

namespace OpakeAdmin\Events\Alert\Inventory;

use Opake\Model\Alert\Alert as OpakeAlert;

class Low extends \Opake\Events\AbstractListener {

	/**
	 * Метод создаёт алерты типов
	 * - TYPE_LOW_INVENTORY
	 * 
	 * @param \Opake\Model\Inventory $inventory
	 */
	public function dispatch($inventory) {

		if (!$inventory->loaded()) {
			return false;
		}

		$count = $this->db->query('select')
			->table('inventory_pack')
			->fields($this->db->expr('SUM(quantity) as count'))
			->group_by('inventory_id')
			->having('inventory_id', $inventory->id)
			->execute()
			->get('count');

		if (!$count) {
			$count = 0;
		}

		$alert = $this->orm->get('Alert_Alert')
			->where('object_id', $inventory->id)
			->where('type', OpakeAlert::TYPE_LOW_INVENTORY)
			->find();

		if ($alert->loaded()) {
			if (!$inventory->min_level || $inventory->min_level <= $count) {
				$alert->delete();
			} else {
				$alert->setObject([
				    'quantity' => $count,
				    'min' => $inventory->min_level
				]);
				$alert->save();
			}
		} elseif ($inventory->min_level && $inventory->min_level > $count) {
			$alert->object_id = $inventory->id;
			$alert->type = OpakeAlert::TYPE_LOW_INVENTORY;
			$alert->title = $inventory->name;
			$alert->organization_id = (int) $inventory->organization_id;
			$alert->subtitle = $alert->statuses[OpakeAlert::TYPE_LOW_INVENTORY];
			$alert->setObject([
			    'quantity' => $count,
			    'min' => $inventory->min_level
			]);
			$alert->save();
		}
	}

}
