<?php

use \Console\Migration\BaseMigration;

class BookingSheetTemplateFields extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `booking_sheet_template_fields` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `booking_sheet_template_id` int(11) unsigned DEFAULT NULL,
			  `field` int(11) unsigned DEFAULT NULL,
			  `x` int(11) unsigned DEFAULT NULL,
			  `y` int(11) unsigned DEFAULT NULL,
			  `active` tinyint(1) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}
