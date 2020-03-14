<?php

namespace Opake\Model\Order\Outgoing;

use Opake\Model\AbstractModel;

class Item extends AbstractModel {

	public $id_field = 'id';
	public $table = 'order_outgoing_item';
	protected $_row = [
	    'id' => null,
	    'order_id' => null,
	    'group_id' => null,
	    'inventory_id' => null,
	    'count' => null
	];
	protected $belongs_to = [
	    'order' => [
		'model' => 'Order_Outgoing',
		'key' => 'order_id'
	    ],
	    'group' => [
		'model' => 'Order_Outgoing_Group_Vendor',
		'key' => 'group_id'
	    ],
	    'inventory' => [
		'model' => 'Inventory',
		'key' => 'inventory_id'
	    ]
	];

	public function get($property) {
		if ($property === 'supply_chain') {
			return $this->inventory->supplies->where('vendor_id', $this->group->vendor_id)->find();
		}
	}

	protected function deleteInternal()
	{
		$this->pixie->events->fireEvent('order.delete_item', $this);
		parent::deleteInternal();
	}

	public function toArray() {
		$inventory = $this->inventory;

		$stock = $this->pixie->db->query('select')
			->table('inventory_pack')
			->fields($this->pixie->db->expr('SUM(quantity) as count'))
			->group_by('inventory_id')
			->having('inventory_id', $inventory->id)
			->execute()
			->get('count');

		return [
		    'id' => (int) $this->id,
		    'inventory' => [
			'id' => (int) $inventory->id,
			'name' => $inventory->name,
			'desc' => $inventory->desc,
			'image' => $inventory->getImage(null, 'tiny'),
			'min_level' => $inventory->min_level,
			'stock' => $stock
		    ],
		    'count' => (int) $this->count
		];
	}

}
