<?php

namespace OpakeAdmin\Events\Alert\Inventory;

use Opake\Model\Alert\Alert as OpakeAlert;

class Missing extends \Opake\Events\AbstractListener {

	/**
	 * Метод создаёт алерты типов
	 * - TYPE_MISSING_INVENTORY
	 * 
	 * @param \Opake\Model\Inventory $inventory
	 */
	public function dispatch($inventory) {

		if (!$inventory->loaded()) {
			return false;
		}
/*
		$alert = $this->orm->get('Alert_Alert')
			->where('object_id', $inventory->id)
			->where('type', OpakeAlert::TYPE_MISSING_INVENTORY)
			->where('phase', OpakeAlert::PHASE_REQUIRES_ACTION)
			->find();

		if ($alert->loaded()) {
			if ($filled) {
				$alert->phase = OpakeAlert::PHASE_RESOLVED;
				$alert->subtitle = $alert->statuses[$alert->type][$alert->phase];
				$alert->save();
			}
		} elseif (!$filled) {
			$alert->object_id = $inventory->id;
			$alert->type = OpakeAlert::TYPE_MISSING_INFO;
			$alert->phase = OpakeAlert::PHASE_REQUIRES_ACTION;
			$alert->title = $inventory->name;
			$alert->organization_id = (int) $inventory->organization_id;
			$alert->subtitle = sprintf($alert->statuses[$alert->type][$alert->phase], '');
			$alert->setObject([
			    'image' => $inventory->getImage(),
			    'name' => $inventory->name,
			    'type' => $inventory->type,
			    'description' => $inventory->desc,
			]);
			$alert->save();
		}
*/
	}

}
