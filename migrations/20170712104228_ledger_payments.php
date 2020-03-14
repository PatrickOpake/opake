<?php

use \Console\Migration\BaseMigration;

class LedgerPayments extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_ledger_payment_activity` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `applied_payment_id` int(11) unsigned DEFAULT NULL,
			  `payment_type` tinyint(4) DEFAULT NULL,
			  `custom_payment_type` varchar(150) DEFAULT NULL,
			  `activity_date` date DEFAULT NULL,
			  `amount` int(11) DEFAULT NULL,
			  `notes` varchar(2048) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}
