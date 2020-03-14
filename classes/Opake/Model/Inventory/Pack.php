<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;

class Pack extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'inventory_pack';
	protected $_row = [
		'id' => null,
		'inventory_id' => null,
		'location_id' => null,
		'distributor_id' => null,
		'order_item_id' => null,
		'exp_date' => null,
		'quantity' => null
	];

	protected $belongs_to = [
		'inventory' => [
			'model' => 'Inventory',
			'key' => 'inventory_id'
		],
		'location' => [
			'model' => 'Location_Storage',
			'key' => 'location_id'
		],
		'distributor' => [
			'model' => 'Vendor',
			'key' => 'distributor_id'
		]
	];

	public function fromArray($data)
	{
		if (!empty($data->quantity)) {
			$data->quantity = $data->quantity;
		}
		if (!empty($data->location)) {
			$data->location_id = $data->location->id;
		}

		return $data;
	}

	public function toArray()
	{
		return [
			'id' => $this->id(),
			'quantity' => (int)$this->quantity,
			'location' => $this->location->toArray(),
			'site' => $this->location->site->toArray(),
			'exp_date' => $this->exp_date
		];
	}

}
