<?php

namespace Opake\Model;

use Opake\Helper\Config;
use Opake\Helper\TimeFormat;

class Inventory extends AbstractModel
{
	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';

	const COMPLETE_STATUS_COMPLETE = 1;
	const COMPLETE_STATUS_INCOMPLETE = 0;

	const ORIGIN_UNKNOWN = 0;
	const ORIGIN_CUSTOM_RECORD = 1;

	public $id_field = 'id';
	public $table = 'inventory';
	protected $_row = array(
		'id' => null,
		'organization_id' => null,
		'site_id' => null,
		'manf_id' => null,
		'uom_id' => null,
		'name' => '', // item_name
		'desc' => '',
		'mmis' => '',
		'status' => self::STATUS_ACTIVE,
		'type' => '',
		'unit_price' => null,
		'image_id' => null,
		'time_create' => null,
		'time_update' => null,
		'qty_per_uom' => null, //qpu
		'total_units' => null, //unit
		'item_number' => '', //item
		'is_remanufacturable' => 0,
		'is_resterilizable' => 0,
		'is_reusable' => 0,
		'is_generic' => 0,
		'is_implantable' => 0,
		'is_latex' => 0,
		'is_hazardous' => 0,
		'hims_indicator' => null,
		'unspsc' => null,
		'min_level' => null, //par_min
		'max_level' => null, //par_max
		'hcpcs' => '',
		'ndc' => '',
		'gln' => '',
		'gtin' => '',
		'charge_amount' => null,
		'auto_order' => null,
		'distributor_name' => '',
		'distributor_catalog' => '',
		'manufacturer_catalog' => '',
		'barcode' => '',
		'barcode_type' => '',
		'shipping_type' => '',
		'unit_weight' => null,
		'complete_status' => self::COMPLETE_STATUS_INCOMPLETE,
		'origin' => null
	);

	protected $belongs_to = [
		'manufacturer' => [
			'model' => 'Vendor',
			'key' => 'manf_id',
		],
		'image' => [
			'model' => 'UploadedFile_Image',
			'key' => 'image_id'
		],
		'uom' => [
			'model' => 'Inventory_UOM',
			'key' => 'uom_id'
		]
	];

	protected $has_many = [
		'packs' => [
			'model' => 'Inventory_Pack',
			'key' => 'inventory_id',
			'cascade_delete' => true
		],
		'distributors' => [
			'model' => 'Vendor',
			'through' => 'inventory_supply',
			'key' => 'inventory_id',
			'foreign_key' => 'vendor_id'
		],
		'supplies' => [
			'model' => 'Inventory_Supply',
			'key' => 'inventory_id',
			'cascade_delete' => true
		],
		'order_items' => [
			'model' => 'Order_item',
			'key' => 'inventory_id',
			'cascade_delete' => true
		],
		'codes' => [
			'model' => 'Inventory_Code',
			'key' => 'inventory_id',
			'cascade_delete' => true
		],
		'kit' => [
			'model' => 'Inventory_Kit',
			'key' => 'inventory_id',
			'cascade_delete' => true
		],
		'card_staff_items' => [
			'model' => 'Card_Staff_Item',
			'key' => 'inventory_id',
			'cascade_delete' => true
		],
		'pref_card_staff_items' => [
			'model' => 'PrefCard_Staff_Item',
			'key' => 'inventory_id',
			'cascade_delete' => true
		],
		'substitutes' => [
			'model' => 'Inventory',
			'through' => 'inventory_substitutes',
			'key' => 'item_id',
			'foreign_key' => 'substitute_id'
		],
	];

	protected $formatters = [
		'InventoryReportFormatter' => [
			'class' => '\Opake\Formatter\Inventory\InventoryReportFormatter'
		],
		'InventoryDynamicFieldsFormatter' => [
			'class' => '\Opake\Formatter\Inventory\InventoryDynamicFieldsFormatter'
		]
	];

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->error('You must specify Item Name');
		$validator->field('item_number')->rule('filled')->error('You must specify Item Number');
		$validator->field('type')->rule('filled')->error('You must specify Type');
		$validator->field('item_number')->rule('unique', $this)->error(sprintf('Item with Item Number %s already exists', $this->item_number));

		$validator->field('hcpcs')->rule('max_length', 10)->error('The HCPCS must be less than or equal to 10 characters');
		$validator->field('qty_per_uom')->rule('numeric', $this)->error('The Quantity Per Unit field must be numeric');

		$validator->field('charge_amount')->rule('decimal')->error('Wrong format of Charge Amount field');
		$validator->field('ndc')->rule('max_length', 10)->error('The National Drug Code  must be less than or equal to 10 characters');
		$validator->field('unit_weight')->rule('numeric', $this)->error('The Unit Weight must be numeric');
		$validator->field('min_level')->rule('numeric', $this)->error('The Par Min must be numeric');
		$validator->field('max_level')->rule('numeric', $this)->error('The Par Max must be numeric');
		$validator->field('total_units')->rule('numeric', $this)->error('The Units in Stock must be numeric');

		$validator->field('min_level')->rule('callback', function ($min_level, $validator) {
			return $min_level < $validator->get('max_level');
		})->error('Par Level Min should be less than the Par Level Max')->condition('max_level')->rule('filled');
		return $validator;
	}

	public function save()
	{
		$now = TimeFormat::formatToDBDatetime(new \DateTime());
		if (!$this->time_create) {
			$this->time_create = $now;
		}

		$this->time_update = $now;

		parent::save();
	}

	public function getType()
	{
		if ($this->type) {
			return $this->pixie->orm->get('Inventory_Type')->where('name', $this->type)->find();
		}

		return null;
	}

	public function getCostMultiplier()
	{
		$inventoryMultiplier = $this->pixie->orm->get('Inventory_Multiplier')
			->where('inventory_id', $this->id)
			->where('organization_id', $this->organization_id)
			->find();
		if ($inventoryMultiplier->loaded()) {
			return $inventoryMultiplier->multiplier;
		}

		if ($this->type) {
			$inventoryTypeMultiplier = $this->pixie->orm->get('Inventory_Multiplier')
				->where('inventory_type_id', $this->getType()->id)
				->where('organization_id', $this->organization_id)
				->find();
			if ($inventoryTypeMultiplier->loaded()) {
				return $inventoryTypeMultiplier->multiplier;
			}
		}

		return null;
	}

	public function getChargeAmount()
	{
		if ($costMultiplier = $this->getCostMultiplier()) {
			$chargeAmount = $this->unit_price * $costMultiplier;
		} else {
			$chargeAmount = $this->unit_price;
		}

		if (number_format($chargeAmount, 2, '.', '') <= number_format($this->getChargeablePrice(), 2, '.', '')) {
			return 0;
		}

		return $chargeAmount;
	}

	public function getChargeablePrice()
	{
		$query = $this->pixie->db->query('select')
			->table('organization')
			->fields('chargeable')
			->where(['id', $this->organization_id])
			->execute()
			->current();

		if ($query) {
			return $query->chargeable;
		}

		return null;
	}

	/**
	 * @param bool $absolute
	 * @param null $size
	 * @return string
	 */
	public function getImage($absolute = false, $size = NULL)
	{
		$path = $this->getImagePath($size);
		if ($absolute) {
			$path = Config::get('app.web') . $path;
		}

		return $path;
	}

	public function getCodesData()
	{
		$result = [];
		if ($this->loaded()) {
			foreach ($this->codes->find_all() as $code) {
				$result[] = $code->toArray();
			}
		}
		return $result;
	}

	public function fromArray($data)
	{
		if (isset($data->manf) && $data->manf) {
			$data->manf_id = $data->manf->id;
		}

		if (isset($data->substitutes) && $data->substitutes) {
			$substitutes = [];
			foreach ($data->substitutes as $item) {
				$substitutes[] = $item->id;
			}
			$data->substitutes = $substitutes;
		}

		if (property_exists($data, 'uom')) {
			if (!empty($data->uom->id)) {
				$data->uom_id = $data->uom->id;
			} else if (!empty($data->uom->name)) {
				$model = $this->pixie->orm->get('Inventory_UOM');
				$uom = $model->addCustomRecord($data->uom->name);
				$data->uom_id = $uom->id();
			} else if ($data->uom === null) {
				$data->uom_id = null;
			}
			unset($data->uom);
		}

		if (isset($data->time_create)) {
			unset($data->time_create);
		}

		return $data;
	}

	public function toArray()
	{
		$packs = [];
		foreach ($this->packs->find_all()->as_array() as $pack) {
			$packs[] = $pack->toArray();
		}

		$codes = [];
		foreach ($this->codes->find_all()->as_array() as $code) {
			$codes[] = $code->toArray();
		}

		$supplies = [];
		foreach ($this->supplies->find_all()->as_array() as $supply) {
			$supplies[] = $supply->toArray();
		}

		$order_items = [];
		foreach ($this->order_items->find_all()->as_array() as $order_item) {
			$order_items[] = $order_item->toArray();
		}

		$kit_items = [];
		foreach ($this->kit->with('item_inventory')->find_all() as $kit_item) {
			$kit_items[] = $kit_item->toArray();
		}

		$substitutes = [];
		foreach ($this->substitutes->find_all() as $item) {
			$substitutes[] = $item->toShortArray();
		}

		$manf = null;
		if($this->manufacturer->loaded()) {
			$manf = $this->manufacturer->toArray();
		}

		$uom = null;
		if($this->uom->loaded()) {
			$uom = $this->uom->toArray();
		}

		return [
			'id' => (int)$this->id,
			'name' => $this->name,
			'desc' => $this->desc,
			'image_id' => $this->image_id,
			'image' => $this->getImage(null, 'default'),
			'image_path' => ($this->image && $this->image->loaded()) ? $this->image->getWebPath() : null,
			'type' => $this->type,
			'manf' => $manf,
			'mmis' => $this->mmis,
			'min_level' => $this->min_level,
			'quantity' => isset($this->quantity) ? (int)$this->quantity : null,
			'code' => $this->hcpcs,
			'organization_id' => $this->organization_id,
			'status' => $this->status,
			'unit_price' => number_format($this->unit_price, 2, '.', ''),
			'time_create' => $this->time_create,
			'time_update' => $this->time_update,
			'uom' => $uom,
			'qty_per_uom' => $this->qty_per_uom,
			'total_units' => $this->total_units,
			'item_number' => $this->item_number,
			'is_remanufacturable' => (int)$this->is_remanufacturable,
			'is_resterilizable' => (int)$this->is_resterilizable,
			'is_reusable' => (int)$this->is_reusable,
			'is_generic' => (int)$this->is_generic,
			'is_implantable' => (int)$this->is_implantable,
			'is_latex' => (int)$this->is_latex,
			'is_hazardous' => (int)$this->is_hazardous,
			'hims_indicator' => $this->hims_indicator,
			'unspsc' => $this->unspsc,
			'max_level' => $this->max_level,
			'hcpcs' => $this->hcpcs,
			'ndc' => $this->ndc,
			'gln' => $this->gln,
			'gtin' => $this->gtin,
			'charge_amount' => number_format($this->getChargeAmount(), 2, '.', ''),
			'packs' => $packs,
			'codes' => $codes,
			'supplies' => $supplies,
			'order_items' => $order_items,
			'kit_items' => $kit_items,
			'auto_order' => (bool)$this->auto_order,
			'substitutes' => $substitutes,
			'distributor_name' => $this->distributor_name,
			'distributor_catalog' => $this->distributor_catalog,
			'manufacturer_catalog' => $this->manufacturer_catalog,
			'barcode' => $this->barcode,
			'barcode_type' => $this->barcode_type,
			'shipping_type' => $this->shipping_type,
			'unit_weight' => $this->unit_weight,
			'cost_multiplier' => $this->getCostMultiplier() ? number_format($this->getCostMultiplier(), 2, '.', '') : '',
		];
	}

	public function toShortArray()
	{
		$stock = $this->pixie->db->query('select')
			->table('inventory_pack')
			->fields($this->pixie->db->expr('SUM(quantity) as count'))
			->group_by('inventory_id')
			->having('inventory_id', $this->id)
			->execute()
			->get('count');

		return [
			'id' => (int) $this->id,
			'name' => $this->name,
			'number' => $this->item_number,
			'full_name' => $this->item_number . ' - ' . $this->name,
			'type' => $this->type,
			'manufacturer' => $this->manufacturer->name,
			'uom' => $this->uom->name,
			'desc' => $this->desc,
			'image' => $this->getImage(null, 'tiny'),
			'min_level' => $this->min_level,
			'quantity' => $stock,
			'unit_price' => number_format($this->unit_price, 2, '.', ''),
			'full_unit_price' => number_format($this->getChargeAmount(), 2, '.', ''),
			'total_units' => $this->total_units,
			'date_created' => date('n/j/Y', strtotime($this->time_create)),
			'complete_status' => (int) $this->complete_status
		];
	}

	public function getFullUnitPrice()
	{
		if ($this->getCostMultiplier()) {
			return $this->unit_price * $this->getCostMultiplier();
		}

		return $this->unit_price;
	}

	public function checkCompleteStatus()
	{
		if (($this->name !== null && $this->name !== '') &&
			($this->item_number !== null && $this->item_number !== '') &&
			($this->type !== null && $this->type !== '')) {
			$this->complete_status = static::COMPLETE_STATUS_COMPLETE;
		} else {
			$this->complete_status = static::COMPLETE_STATUS_INCOMPLETE;
		}
	}

	/**
	 * @return \Opake\Model\UploadedFile\Image
	 */
	public function getImageModel()
	{
		if ($this->image_id) {
			if ($this->image->loaded()) {
				return $this->image;
			}
			if (!$this->image->loaded() && $this->image_id) {
				$model = $this->pixie->orm->get('UploadedFile_Image', $this->image_id);
				if ($model->loaded()) {
					return $model;
				}
			}
		}

		return null;
	}

	public function addCustomRecord($organizationId, $name, $type)
	{
		$this->where('name', $name);
		if ($type) {
			$this->where('type', $type);
		}
		$this->where('organization_id', $organizationId);
		$existedModel = $this->find();
		if ($existedModel->loaded()) {
			return $existedModel;
		}

		/** @var Inventory $newModel */
		$newModel = $this->pixie->orm->get($this->model_name);
		$newModel->organization_id = $organizationId;
		$newModel->name = $name;
		$newModel->type = $type;
		$newModel->origin = static::ORIGIN_CUSTOM_RECORD;

		$newModel->save();

		return $newModel;
	}

	/**
	 * @inheritdoc
	 * @throws \Exception
	 */
	protected function deleteInternal()
	{
		parent::deleteInternal();
		if ($image = $this->getImageModel()) {
			$image->delete();
		}
	}

	/**
	 * @return string
	 */
	protected function getImagePath($size)
	{

		if ($image = $this->getImageModel()) {
			return $image->getThumbnailWebPath($size);
		}

		if ($this->getType()) {
			$path = $this->getType()->getImage($size);
			if ($path) {
				return $path;
			}
		}

		if ($size) {
			return '/i/default-logo_' . $size . '.png';
		}

		return '/i/default-logo.png';
	}
}
