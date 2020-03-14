<?php

use \Console\Migration\BaseMigration;
use Opake\Helper\TimeFormat;

class RedefineMasterInventory extends BaseMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
	$this->query("
		ALTER TABLE `inventory` DROP `pref_vendor_id`;
		ALTER TABLE `inventory` DROP `total_qty`;

		ALTER TABLE `inventory` ADD `distributor_name` VARCHAR(255) NULL;
		ALTER TABLE `inventory` ADD `manufacturer_catalog` VARCHAR(255) NULL;
		ALTER TABLE `inventory` ADD `distributor_catalog` VARCHAR(255) NULL;

		ALTER TABLE `inventory` ADD `barcode` VARCHAR(255) NULL;
		ALTER TABLE `inventory` ADD `barcode_type` VARCHAR(255) NULL;

		ALTER TABLE `inventory` ADD `unit_weight` FLOAT NULL DEFAULT NULL;
		ALTER TABLE `inventory` ADD `shipping_type` VARCHAR(255) NULL DEFAULT NULL;

	");

	$rows = $this->getDb()->query('select')
	    ->table('master_inventory')
	    ->execute();

	$this->getDb()->begin_transaction();

	try {
	    foreach ($rows as $row) {
		    $isExistInventory = false;
		    if($row->opake_id) {
			    $inventory = $this->getDb()->query('select')
				    ->table('inventory')
				    ->where('id', $row->opake_id)
				    ->execute()->as_array();

			    if($inventory) {
				    $isExistInventory = true;
				    $this->getDb()->query('update')
					    ->table('inventory')
					    ->data([
						    'manf_id' => $this->getManfId($row),
						    'name' => $row->item_name,
						    'desc' => $row->desc,
						    'status' => $row->status,
						    'type' => $row->type,
						    'unit_price' => $row->unit_price,
						    'image_id' => $row->image_id,
						    'uom' => $row->uom,
						    'qty_per_uom' => $row->qpu,
						    'total_units' => $row->unit,
						    'item_number' => $row->item,
						    'is_remanufacturable' => $row->is_remanufacturable,
						    'is_resterilizable' => $row->is_resterilizable,
						    'is_reusable' => $row->is_reusable,
						    'is_generic' => $row->is_generic,
						    'min_level' => $row->par_min,
						    'max_level' => $row->par_max,
						    'hcpcs' => $row->hcpcs,
						    'ndc' => $row->ndc,
						    'gln' => $row->gln,
						    'gtin' => $row->gtin,
						    'charge_amount' => $row->charge_amount,
						    'distributor_name' => $row->distributor_name,
						    'distributor_catalog' => $row->distributor_catalog,
						    'manufacturer_catalog' => $row->manufacturer_catalog,
						    'barcode' => $row->barcode,
						    'barcode_type' => $row->barcode_type,
						    'shipping_type' => $row->shipping_type,
						    'unit_weight' => $row->unit_weight,

					    ])
					    ->where('id', $row->opake_id)
					    ->execute();

				    $this->saveSubs($row->opake_id);

			    }
		    }

		    if(!$isExistInventory) {
			    $this->getDb()->query('insert')->table('inventory')
				    ->data([
					    'organization_id' => $row->organization_id,
					    'manf_id' => $this->getManfId($row),
					    'name' => $row->item_name,
					    'desc' => $row->desc,
					    'status' => $row->status,
					    'type' => $row->type,
					    'unit_price' => $row->unit_price,
					    'image_id' => $row->image_id,
					    'time_create' => TimeFormat::formatToDBDatetime(new \DateTime()),
					    'time_update' => TimeFormat::formatToDBDatetime(new \DateTime()),
					    'uom' => $row->uom,
					    'qty_per_uom' => $row->qpu,
					    'total_units' => $row->unit,
					    'item_number' => $row->item,
					    'is_remanufacturable' => $row->is_remanufacturable,
					    'is_resterilizable' => $row->is_resterilizable,
					    'is_reusable' => $row->is_reusable,
					    'is_generic' => $row->is_generic,
					    'min_level' => $row->par_min,
					    'max_level' => $row->par_max,
					    'hcpcs' => $row->hcpcs,
					    'ndc' => $row->ndc,
					    'gln' => $row->gln,
					    'gtin' => $row->gtin,
					    'charge_amount' => $row->charge_amount,
					    'distributor_name' => $row->distributor_name,
					    'distributor_catalog' => $row->distributor_catalog,
					    'manufacturer_catalog' => $row->manufacturer_catalog,
					    'barcode' => $row->barcode,
					    'barcode_type' => $row->barcode_type,
					    'shipping_type' => $row->shipping_type,
					    'unit_weight' => $row->unit_weight,
				    ])
				    ->execute();

			    $this->saveSubs($this->getDb()->insert_id());
		    }
	    }
	    $this->getDb()->commit();
	} catch (\Exception $e) {
	    $this->getDb()->rollback();
	    throw $e;
	}

	$this->query("
		DROP TABLE `master_inventory`;
		DROP TABLE `master_items_substitutes`;
	");
    }

    protected function getManfId($row)
    {
	    $manf_id = null;
	    if($row->manufacturer_name) {
		    $manufacturer = $this->getDb()->query('select')
			    ->table('vendor')->where([
				    ['name', $row->manufacturer_name],
				    ['organization_id', $row->organization_id]
			    ])->execute()->as_array();
		    if($manufacturer) {
			    $manf_id = $manufacturer[0]->id;
			    if (!$manufacturer[0]->is_manf) {
				    $this->getDb()->query('update')
					    ->table('vendor')
					    ->data([
						    'is_manf' => 1
					    ])
					    ->where('id', $manf_id)
					    ->execute();
			    }
		    } else {
			    $this->getDb()->query('insert')->table('vendor')
				    ->data([
					    'organization_id' => $row->organization_id,
					    'name' => $row->manufacturer_name,
					    'is_manf' =>1,
				    ])
				    ->execute();
			    $manf_id = $this->getDb()->insert_id();
		    }
	    }

	    return $manf_id;
    }

    protected function saveSubs($id)
    {
	    $this->getDb()->query('delete')
		    ->table('inventory_substitutes')
		    ->where('item_id', $id)
		    ->execute();

	    $substitutes = $this->getDb()->query('select')
		    ->table('master_items_substitutes')
		    ->where('item_id', $id)
		    ->execute()->as_array();
	    if($substitutes) {
		    foreach ($substitutes as $sub) {
			    $this->getDb()->query('insert')->table('inventory_substitutes')
				    ->data([
					    'item_id' => $id,
					    'substitute_id' => $sub->substitute_id
				    ])
				    ->execute();
		    }
	    }
    }
}
