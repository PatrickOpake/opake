<?php

namespace Opake\Model\Order;

use Opake\Model\AbstractModel;

class Image extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'order_image';
	protected $_row = [
		'id' => null,
		'order_id' => null,
		'index' => null,
		'image_id' => ''
	];

	protected $belongs_to = [
		'order' => [
			'model' => 'Order',
			'key' => 'order_id'
		],
		'image' => [
			'model' => 'UploadedFile_Image',
			'key' => 'image_id'
		]
	];

	public function toArray()
	{
		return [
			'index' => $this->index,
			'image' => $this->image->loaded() ? $this->image->getWebPath() : ''
		];
	}

}
