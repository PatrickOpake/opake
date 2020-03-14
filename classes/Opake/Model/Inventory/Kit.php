<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;

class Kit extends AbstractModel
{

	public $id_field = 'inventory_id';
	public $table = 'inventory_kit_items';
	protected $_row = [
		'inventory_id' => null,
		'item_id' => null,
		'quantity' => null,
	];

	protected $belongs_to = [
		'item_inventory' => [
			'model' => 'Inventory',
			'key' => 'item_id'
		]
	];

	public function toArray()
	{
		return [
			'item_id' => (int)$this->item_inventory->id,
			'inventory_id' => (int)$this->inventory_id,
			'inventory' => $this->item_inventory->toShortArray(),
			'quantity' => (int)$this->quantity,
		];
	}

	public function save()
	{
		$query = $this->conn->query('insert')->table($this->table);
		$query->data($this->_row);
		$query->execute();
	}
}
