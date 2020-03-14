<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;

class Multiplier extends AbstractModel
{
	const MULTIPLIER_TYPE_ITEM_NAME = 0;
	const MULTIPLIER_TYPE_ITEM_TYPE = 1;

	public $id_field = 'id';
	public $table = 'inventory_multiplier';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'site_id' => null,
		'type' => self::MULTIPLIER_TYPE_ITEM_NAME,
		'inventory_id' => null,
		'inventory_type_id' => null,
		'multiplier' => null
	];

	protected $belongs_to = [
		'inventory' => [
			'model' => 'Inventory',
			'key' => 'inventory_id'
		],
		'inventory_type' => [
			'model' => 'Inventory_Type',
			'key' => 'inventory_type_id'
		]
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('multiplier')->rule('filled')->error('You must specify multiplier');
		if ($this->type == self::MULTIPLIER_TYPE_ITEM_NAME) {
			$validator->field('inventory_id')->rule('filled')->error('You must specify Item Name');
			$validator->field('inventory_id')->rule('unique', $this)->error('Multiplier for this item name already exists');
		}
		if ($this->type == self::MULTIPLIER_TYPE_ITEM_TYPE) {
			$validator->field('inventory_type_id')->rule('filled')->error('You must specify Item Type');
			$validator->field('inventory_type_id')->rule('unique', $this)->error('Multiplier for this item type already exists');
		}

		return $validator;
	}

	public function fromArray($data)
	{
		if (isset($data->inventory) && $data->inventory) {
			if (isset($data->inventory->id)) {
				$data->inventory_id = $data->inventory->id;
			} else {
				$data->inventory_id = null;
			}
		}

		if (isset($data->inventory_type) && $data->inventory_type) {
			if (isset($data->inventory_type->id)) {
				$data->inventory_type_id = $data->inventory_type->id;
			} else {
				$data->inventory_type_id = null;
			}
		}

		return $data;
	}

	public function toArray()
	{
		return [
			'id' => (int) $this->id,
			'organization_id' => (int) $this->organization_id,
			'site_id' => $this->site_id,
			'inventory_id' => (int) $this->inventory_id,
			'inventory_type_id' => (int) $this->inventory_type_id,
			'type' => (int) $this->type,
			'multiplier' => number_format($this->multiplier, 2, '.', ''),
			'inventory' => $this->getInventoryArray(),
			'inventory_type' => $this->getInventoryTypeArray()
		];
	}

	protected function getInventoryArray()
	{
		if ($this->inventory_id) {
			return [
				'id' => $this->inventory->id,
				'number' => $this->inventory->item_number,
				'name' => $this->inventory->name,
				'full_name' => $this->inventory->item_number . ' - ' . $this->inventory->name,
			];
		}

		return null;
	}

	protected function getInventoryTypeArray()
	{
		if ($this->inventory_type_id) {
			return [
				'id' => $this->inventory_type->id,
				'name' => $this->inventory_type->name
			];
		}

		return null;
	}
}
