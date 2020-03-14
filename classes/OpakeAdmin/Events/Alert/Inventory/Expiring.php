<?php

namespace OpakeAdmin\Events\Alert\Inventory;

use Opake\Model\Alert\Alert as OpakeAlert;
use Opake\Helper\TimeFormat;

class Expiring extends \Opake\Events\AbstractListener {

	/**
	 * Метод создаёт алерты типов
	 * - TYPE_EXPIRING
	 * 
	 * @param \Opake\Model\Inventory $inventory
	 */
	public function dispatch($inventory) {

		if ( !$inventory->loaded() ) {
			return false;
		}

		// получаем паки инвенторя
		$packs = $this->orm->get('Inventory_Pack')
			->where('inventory_id', $inventory->id)
			->where('exp_date', '<', strftime(TimeFormat::DATE_FORMAT_DB, strtotime('+2 week')));

		$exp_date = PHP_INT_MAX;
		$exp_count = 0;
		foreach ($packs->find_all() as $pack) {
			if ($exp_date > strtotime($pack->exp_date)) {
				$exp_date = strtotime($pack->exp_date);
			}
			$exp_count += $pack->quantity;
		}

		$alert = $this->orm->get('Alert_Alert')
			->where('object_id', $inventory->id)
			->where('type', OpakeAlert::TYPE_EXPIRING)
			->find();

		if ($alert->loaded()) {
			if (!$exp_count) {
				$alert->delete();
			} else {
				$alert->setObject([
					'date' => strftime(TimeFormat::DATE_FORMAT_DB, $exp_date),
					'quantity' => $exp_count
				]);
				$alert->save();
			}
		} elseif ($exp_count) {
			$alert->object_id = $inventory->id;
			$alert->type = OpakeAlert::TYPE_EXPIRING;
			$alert->title = $inventory->name;
			$alert->subtitle = $inventory->desc;
			$alert->organization_id = (int) $inventory->organization_id;
			$alert->setObject([
				'date' => strftime(TimeFormat::DATE_FORMAT_DB, $exp_date),
				'quantity' => $exp_count
			]);
			$alert->save();
		}

	}

}
