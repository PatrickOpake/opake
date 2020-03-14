<?php

namespace Opake\Model\Order;

use Opake\Model\AbstractModel;
use Opake\Helper\TimeFormat;

class Outgoing extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'order_outgoing';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'date' => null,
	];

	protected $has_many = [
		'items' => [
			'model' => 'Order_Outgoing_Item',
			'key' => 'order_id'
		],
		'groups' => [
			'model' => 'Order_Outgoing_Group_Vendor',
			'key' => 'order_id'
		],
		'messages' => [
			'model' => 'Order_Outgoing_Mail',
			'key' => 'order_outgoing_id'
		],
	];

	public function isActive()
	{
		return $this->date === null;
	}

	protected function deleteInternal()
	{
		foreach ($this->items->find_all() as $item) {
			$item->delete();
		}
		parent::deleteInternal();
	}

	public function toArray()
	{

		$groups = [];
		foreach ($this->groups->find_all() as $group) {
			$groups[] = $group->toArray();
		}

		$messages = [];
		foreach ($this->messages->find_all() as $message) {
			$messages[] = $message->toArray();
		}

		return [
			'id' => (int)$this->id,
			'date' => $this->date ? TimeFormat::getDate($this->date) : null,
			'groups' => $groups,
			'messages' => $messages
		];
	}

	public function toShortArray()
	{
		return [
			'id' => (int)$this->id,
			'date' => $this->date ? TimeFormat::getDate($this->date) : null,
			'item_count' => isset($this->item_count) ? (int)$this->item_count : 0
		];
	}

}
