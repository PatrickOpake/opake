<?php

namespace Opake\Model\Inventory;

use Opake\Model\AbstractModel;

class Invoice extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'inventory_invoice';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'uploaded_file_id' => null,
		'name' => '',
		'date' => null
	];
	protected $belongs_to = [
		'file' => [
			'model' => 'UploadedFile',
			'key' => 'uploaded_file_id'
		]
	];
	protected $has_many = [
		'manufacturers' => [
			'model' => 'Vendor',
			'through' => 'inventory_invoice_manufacturer',
			'key' => 'invoice_id',
			'foreign_key' => 'vendor_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		],
		'items' => [
			'model' => 'Inventory',
			'through' => 'inventory_invoice_item',
			'key' => 'invoice_id',
			'foreign_key' => 'inventory_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		]
	];

	protected $baseFormatter = [
		'class' => '\Opake\Formatter\Inventory\Invoice\InventoryInvoiceFormatter'
	];
}
