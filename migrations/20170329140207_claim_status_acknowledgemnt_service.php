<?php

use \Console\Migration\BaseMigration;

class ClaimStatusAcknowledgemntService extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_navicure_claim_status_acknowledgment_service` (
				  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				  `claim_id` int(11) DEFAULT NULL,
				  `date` date DEFAULT NULL,
				  `service_code` varchar(15) DEFAULT NULL,
				  `amount` float DEFAULT NULL,
				  `status` int(11) DEFAULT NULL,
				  `note` varchar(1024) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB;
		");
    }
}
