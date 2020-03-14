<?php

namespace Opake\Model\PrefCard\Staff;

use Opake\Model\AbstractModel;

class Item extends AbstractModel
{
	public $table = 'pref_card_staff_item';
	public $id_field = 'id';
	protected $_row = [
		'id' => null,
		'card_id' => null,
		'stage_id' => null,
		'inventory_id' => null,
		'default_qty' => null,
		'position' => null
	];
	protected $belongs_to = [
		'inventory' => [
			'model' => 'Inventory',
			'key' => 'inventory_id'
		],
		'stage' => [
			'model' => 'PrefCard_Stage',
			'key' => 'stage_id'
		]
	];

	public function fromArray($data)
	{
		if (isset($data->inventory) && $data->inventory) {
			if (isset($data->inventory->id)) {
				$data->inventory_id = $data->inventory->id;
			} else if (!empty($data->inventory->name) && !empty($data->inventory->org_id)) {
				$model = $this->pixie->orm->get('Inventory');
				$inventory = $model->addCustomRecord($data->inventory->org_id, $data->inventory->name, null);
				$data->inventory_id = $inventory->id;
			} else {
				$data->inventory_id = null;
			}
		}

		return $data;
	}

	public function toArray()
	{
		return [
			'id' => $this->id(),
			'stage_id' => $this->stage_id,
			'inventory_id' => (int) $this->inventory->id,
			'inventory' => $this->getInventoryArray(),
			'default_qty' => (int) $this->default_qty,
			'position' => (int) $this->position
		];
	}

	protected function getInventoryArray()
	{
		if ($this->hasInventory()) {
			return [
				'id' => $this->inventory->id,
				'number' => $this->inventory->item_number,
				'name' => $this->inventory->name,
				'full_name' => $this->inventory->item_number . ' - ' . $this->inventory->name,
				'desc' => $this->inventory->desc,
				'uom' => $this->inventory->uom->name,
				'manufacturer' => $this->inventory->manufacturer->name,
				'full_unit_price' => number_format($this->inventory->getChargeAmount(), 2, '.', '')
			];
		}

		return null;
	}

	protected function hasInventory() {
		if ($this->inventory_id) {
			$inventory = $this->pixie->orm->get('Inventory', $this->inventory_id);
			if ($inventory->loaded()) {
				return true;
			}
		}

		return false;
	}

	public function get($property)
	{
		if (!$this->loaded()) {
			if ($property === 'inventory') {
				return $this->pixie->orm->get('Inventory', $this->inventory_id);
			}
		}
	}

}
