<?php

namespace OpakeApi\Model;

use Opake\Model\Order as OpakeOrder;

class Order extends OpakeOrder
{
	use Api;

	public function fromArray($data)
	{
		return $this->apiFill([
			'vendorid' => 'vendor_id',
			'shippingtype' => 'shipping_type',
			'shippingcost' => 'shipping_cost'
		], $data);
	}

}
