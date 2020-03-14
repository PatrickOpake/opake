<?php

use \Console\Migration\BaseMigration;

class Efax extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `efax_inbound_fax` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `organization_id` int(11) DEFAULT NULL,
			  `site_id` int(11) DEFAULT NULL,
			  `to_fax` varchar(255) DEFAULT NULL,
			  `from_fax` varchar(255) DEFAULT NULL,
			  `sent_date` datetime DEFAULT NULL,
			  `received_date` datetime DEFAULT NULL,
			  `scrypt_sfax_id` int(11) DEFAULT NULL,
			  `saved_file_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");

	    $this->query("
	        CREATE TABLE `efax_inbound_fax_read_status` (
			  `user_id` int(11) unsigned NOT NULL,
			  `fax_id` int(11) unsigned NOT NULL,
			  PRIMARY KEY (`user_id`, `fax_id`)
			) ENGINE=InnoDB;
	    ");
    }
}
