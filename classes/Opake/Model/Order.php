<?php

namespace Opake\Model;

use Opake\Helper\TimeFormat;

class Order extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'order';
	protected $_row = array(
		'id' => null,
		'po_id' => null,
		'organization_id' => null,
		'vendor_id' => null,
		'date' => null,
		'shipping_type' => null,
		'shipping_cost' => 0,
		'status' => 0
	);

	protected $belongs_to = array(
		'organization' => array(
			'model' => 'Organization',
			'key' => 'organization_id',
		),
		'vendor' => array(
			'model' => 'Vendor',
			'key' => 'vendor_id',
		),
	);
	protected $has_many = [
		'items' => [
			'model' => 'Order_Item',
			'key' => 'order_id',
			'cascade_delete' => true
		],
		'images' => [
			'model' => 'Order_Image',
			'key' => 'order_id',
			'cascade_delete' => true
		]
	];

	const STATUS_OPEN = 0;
	const STATUS_INCOMPLETE = 1;
	const STATUS_COMPLETE = 2;

	protected static $statuses = [
		self::STATUS_OPEN => 'Open',
		self::STATUS_INCOMPLETE => 'Incomplete',
		self::STATUS_COMPLETE => 'Complete'
	];

	public function images()
	{
		$images = $this->images->find_all()->as_array();
		usort($images, function ($a, $b) {
			return $a->index > $b->index;
		});
		return $images;
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('organization_id')->rule('filled')->error('You must specify organization');
		$validator->field('vendor_id')->rule('filled')->error('You must specify distributor');
		$validator->field('po_id')->rule('unique', $this)->error(sprintf('Order with P.O.# %s already exists', $this->po_id));
		return $validator;
	}

	public function getStatus()
	{
		return self::$statuses[$this->status];
	}

	public static function getStatuses()
	{
		return self::$statuses;
	}

	public function toArray()
	{

		$items = [];
		foreach ($this->items->find_all() as $item) {
			$items[] = $item->toArray();
		}

		$data = [
			'id' => $this->id,
			'po_id' => $this->po_id,
			'date' => TimeFormat::getDate($this->date),
			'item_count' => isset($this->item_count) ? $this->item_count : 0,
			'status' => $this->status,
			'status_name' => $this->getStatus(),
			'shipping_type' => $this->shipping_type,
			'shipping_cost' => $this->shipping_cost,
			'vendor' => $this->vendor->name,
			'images' => [],
			'items' => $items
		];

		foreach ($this->images->find_all() as $image) {
			$data['images'][] = $image->toArray();
		}

		return $data;
	}

	public function toShortArray()
	{
		$data = [
			'id' => $this->id,
			'date' => TimeFormat::getDate($this->date),
			'item_count' => isset($this->item_count) ? $this->item_count : 0,
			'images' => [],
			'organization_name' => $this->organization->name,
		];

		foreach ($this->images->find_all() as $image) {
			$data['images'][] = $image->toArray();
		}

		return $data;
	}

	public function save()
	{
		$this->date = TimeFormat::formatToDBDatetime(new \DateTime());

		parent::save();
	}
}
