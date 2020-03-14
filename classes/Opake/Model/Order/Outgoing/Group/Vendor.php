<?php

namespace Opake\Model\Order\Outgoing\Group;

use Opake\Model\AbstractModel;

class Vendor extends AbstractModel {

	public $id_field = 'id';
	public $table = 'order_outgoing_vendor';
	protected $_row = [
	    'id' => null,
	    'order_id' => null,
	    'vendor_id' => null,
	    'received_order_id' => null,
	];
	protected $belongs_to = [
	    'order' => [
		'model' => 'Order_Outgoing',
		'key' => 'order_id'
	    ],
	    'vendor' => [
		'model' => 'Vendor',
		'key' => 'vendor_id'
	    ],
	];
	protected $has_many = [
		'items' => [
			'model' => 'Order_Outgoing_Item',
			'key' => 'group_id',
			'cascade_delete' => true
		]
	];

	public function toArray() {
		$vendor = $this->vendor;

		$items = [];
		foreach ($this->items->find_all() as $item) {
			$items[] = $item->toArray();
		}

		return [
		    'id' => (int) $this->id,
		    'vendor' => [
			'id' => $vendor->id,
			'name' => $vendor->name,
			'email' => $vendor->email
		    ],
		    'items' => $items,
		    'received_order_id' => $this->received_order_id,
		];
	}

}
