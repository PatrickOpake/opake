<?php

namespace Opake\Model\Cases;

class InventoryItem extends \Opake\Model\AbstractModel
{

	public $table = 'case_inventory_item';
	protected $_row = [
		'id' => null,
		'case_id' => null,
		'inventory_id' => null,
		'quantity' => null
	];
	protected $belongs_to = [
		'inventory' => [
			'model' => 'Inventory',
			'key' => 'inventory_id'
		]
	];



	public function toArray()
	{
		return [
			'id' => $this->id,
			'inventory_id' => (int)$this->inventory->id,
			'inventory' => [
				'name' => $this->inventory->name,
				'type' => $this->inventory->type,
				'image' => $this->inventory->getImage(null, 'tiny'),
				'manufacturer' => $this->inventory->manufacturer->name
			],
			'quantity' => (int)$this->quantity
		];
	}
}
