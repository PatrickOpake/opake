<?php

use \Console\Migration\BaseMigration;

class InventoryInvoiceItemsUpdates extends BaseMigration
{
    public function change()
    {
	$this->query("
		DROP TABLE `inventory_invoice_item`;
	        CREATE TABLE `inventory_invoice_item` (
		  `invoice_id` int(10) unsigned NOT NULL,
		  `inventory_id` int(10) unsigned NOT NULL,
		  `order` int(10) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`invoice_id`,`inventory_id`)
		) ENGINE=InnoDB;
	");
    }
}
