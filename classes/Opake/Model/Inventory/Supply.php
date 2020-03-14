<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;

class Supply extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'inventory_supply';
	protected $_row = [
		'id' => null,
		'inventory_id' => null,
		'vendor_id' => null,
		'device_id' => null
	];
	protected $belongs_to = [
		'inventory' => [
			'model' => 'Inventory',
			'key' => 'inventory_id'
		],
		'distributor' => [
			'model' => 'Vendor',
			'key' => 'vendor_id'
		]
	];

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'vendor' => [
				'id' => (int)$this->distributor->id,
				'name' => $this->distributor->name,
			],
			'device_id' => $this->device_id,
		];
	}

}
