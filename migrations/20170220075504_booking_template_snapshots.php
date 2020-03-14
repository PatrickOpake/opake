<?php

use \Console\Migration\BaseMigration;

class BookingTemplateSnapshots extends BaseMigration
{
    public function change()
    {
	    $this->query("
	        CREATE TABLE `booking_sheet_template_snapshot` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `booking_id` int(11) DEFAULT NULL,
			  `original_template_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
	    ");

	    $this->query("
	        CREATE TABLE `booking_sheet_template_snapshot_fields` (
	        	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `booking_sheet_template_snapshot_id` int(11) NOT NULL,
				  `field` int(11) NOT NULL,
				  `x` int(11) DEFAULT NULL,
				  `y` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB;
	    ");

	    $this->query("
	        ALTER TABLE `booking_sheet` DROP `template_id`;
	    ");
    }
}
