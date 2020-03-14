<?php

use \Console\Migration\BaseMigration;

class ClaimStatusAcknowledgment extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_navicure_claim_status_acknowledgment` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `claim_id` int(11) DEFAULT NULL,
			  `date` date DEFAULT NULL,
			  `amount` float DEFAULT NULL,
			  `status` tinyint(4) DEFAULT NULL,
			  `note` varchar(1024) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}
