<?php

namespace OpakeApi\Model\Order;

use Opake\Model\Order\Item as OpakeItem;
use OpakeApi\Model\Api;

class Item extends OpakeItem
{
	use Api;

	public function fromArray($data)
	{
		return $this->apiFill([
			'id' => 'inventory_id',
			'locationid' => 'location_id',
			'expdate' => 'exp_date',
			'receivedqty' => 'received',
			'damagedqty' => 'damaged',
			'missingqty' => 'missing',
			'unitsreceived' => 'units_received',
			'quantityperunit' => 'unit_quantity',
			'unitofmeasurename' => 'uom',
			'price' => 'price'
		], $data);
	}

}
