<?php

use \Console\Migration\BaseMigration;

class InventoryInvoices extends BaseMigration
{
    public function change()
    {
	$this->query("
		CREATE TABLE IF NOT EXISTS `inventory_invoice` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `organization_id` int(10) unsigned NOT NULL,
		  `uploaded_file_id` INT(10) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `date` date NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		ALTER TABLE `inventory_invoice` ADD KEY `organization_id` (`organization_id`);
	");

	$this->query("
	        CREATE TABLE `inventory_invoice_manufacturer` (
		  `invoice_id` int(10) unsigned NOT NULL,
		  `vendor_id` int(10) unsigned NOT NULL,
		  `order` int(10) unsigned NOT NULL DEFAULT '0',
		  PRIMARY KEY (`invoice_id`,`vendor_id`)
		) ENGINE=InnoDB;
	");

	$this->query("
            CREATE TABLE `inventory_invoice_item` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `invoice_id` int(10) unsigned NOT NULL,
                `inventory_id` int(10) unsigned NOT NULL,
                `page` SMALLINT NOT NULL,
                `x` FLOAT NOT NULL,
                `y` FLOAT NOT NULL,
                `width` FLOAT NOT NULL,
                `height` FLOAT NOT NULL,
                PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	    ALTER TABLE `inventory_invoice_item` ADD INDEX `IDX_invoice_id` (`invoice_id`);
	");
    }
}
