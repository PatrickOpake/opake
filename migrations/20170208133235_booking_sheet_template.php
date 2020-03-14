<?php

use \Console\Migration\BaseMigration;

class BookingSheetTemplate extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `booking_sheet_template` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `organization_id` int(11) unsigned DEFAULT NULL,
			  `type` int(11) unsigned DEFAULT NULL,
			  `name` varchar(255) DEFAULT NULL,
			  `is_all_sites` tinyint(1) unsigned DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");

	    $this->query("
	        CREATE TABLE `booking_sheet_template_site` (
			  `booking_sheet_template_id` int(11) unsigned NOT NULL,
			  `site_id` int(11) unsigned NOT NULL,
			  `order` int(11) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`booking_sheet_template_id`,`site_id`)
			) ENGINE=InnoDB;
	    ");
    }
}
