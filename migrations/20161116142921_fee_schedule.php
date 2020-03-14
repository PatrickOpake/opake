<?php

use \Console\Migration\BaseMigration;

class FeeSchedule extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_fee_schedule_info` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `organization_id` int(11) DEFAULT NULL,
			  `site_id` int(11) DEFAULT NULL,
			  `cbsa` varchar(255) DEFAULT NULL,
			  `effective_date` date DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");

	    $this->query("
			CREATE TABLE `billing_fee_schedule` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `organization_id` int(11) DEFAULT NULL,
			  `site_id` int(11) DEFAULT NULL,
			  `hcpcs` varchar(128) DEFAULT NULL,
			  `mod` varchar(32) DEFAULT NULL,
			  `procedure_indicator` varchar(1) DEFAULT NULL,
			  `amount` float DEFAULT NULL,
			  `fc_mod_amount` float DEFAULT NULL,
			  `fb_mod_amount` float DEFAULT NULL,
			  `penalty_price` float DEFAULT NULL,
			  `fc_mod_penalty_price` float DEFAULT NULL,
			  `fb_mod_penalty_price` float DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
	    ");
    }
}
