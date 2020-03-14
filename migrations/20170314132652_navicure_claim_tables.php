<?php

use \Console\Migration\BaseMigration;

class NavicureClaimTables extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_navicure_log` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `claim_id` int(11) DEFAULT NULL,
			  `transaction` tinyint(3) DEFAULT NULL,
			  `direction` tinyint(1) DEFAULT NULL,
			  `status` tinyint(3) DEFAULT NULL,
			  `error` tinyint(3) DEFAULT NULL,
			  `data` mediumtext,
			  `time` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");

	    $this->query("
			CREATE TABLE `billing_navicure_claim` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `case_id` int(11) DEFAULT NULL,
			  `active` tinyint(3) DEFAULT NULL,
			  `status` tinyint(3) DEFAULT NULL,
			  `additional_status` tinyint(3) DEFAULT NULL,
			  `error` varchar(1024) DEFAULT NULL,
			  `last_update` datetime DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
	    ");
    }
}
