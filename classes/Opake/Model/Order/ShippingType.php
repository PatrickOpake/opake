<?php

namespace Opake\Model\Order;

use Opake\Model\AbstractModel;

class ShippingType extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'order_shipping_type';
	protected $_row = [
		'id' => null,
		'name' => '',
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->rule('min_length', 2)->error('Invalid Name');
		$validator->field('name')->rule('unique', $this)->error(sprintf('Shipping Type with name %s already exists', $this->name));
		return $validator;
	}

}
