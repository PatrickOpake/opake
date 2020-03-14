<?php

namespace Opake\Model\Order;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;

class Item extends AbstractModel
{

	const STATUS_OPEN = 0;
	const STATUS_INCOMPLETE = 1;
	const STATUS_COMPLETE = 2;

	public $id_field = 'id';
	public $table = 'order_item';
	protected $_row = [
		'id' => null,
		'order_id' => null,
		'inventory_id' => null,
		'location_id' => null,
		'exp_date' => null,
		'ordered' => null,
		'received' => null,
		'damaged' => '',
		'missing' => '',
		'units_received' => '',
		'unit_quantity' => '',
		'uom' => '',
		'price' => '',
		'status' => 0
	];

	protected $has_many = [
		'packs' => [
			'model' => 'Inventory_Pack',
			'key' => 'order_item_id',
			'cascade_delete' => true
		]
	];

	protected $belongs_to = [
		'order' => [
			'model' => 'Order',
			'key' => 'order_id'
		],
		'inventory' => [
			'model' => 'Inventory',
			'key' => 'inventory_id'
		]
	];

	public function getUnitsQuantity()
	{
		return round($this->units_received * $this->unit_quantity, 2);
	}

	public function getUnitPrice()
	{
		if ($this->price && $this->units_received != 0.0) {
			return round($this->price / $this->units_received, 2);
		}
	}

	public function getOrdered()
	{
		if (empty($this->ordered)) {
			$result = 0;
			foreach ($this->packs->find_all() as $pack) {
				$result += $pack->quantity;
			}
			return $result;
		}
		return $this->ordered;
	}

	public function toArray()
	{
		return [
			'id' => (int)$this->id,
			'damaged' => $this->damaged,
			'received' => $this->received,
			'missing' => $this->missing,
			'price' => $this->price,
			'exp_date' => TimeFormat::getDate($this->exp_date),
			'status' => (int)$this->status,
			'ordered' => $this->getOrdered(),
			'unitsQuantity' => $this->getUnitsQuantity(),
			'unitPrice' => $this->getUnitPrice()
		];
	}

	public function save()
	{
		$this->exp_date = TimeFormat::formatToDBDatetime(new \DateTime());

		parent::save();
	}
}
