<?php

use \Console\Migration\BaseMigration;

class BillingLedgerInterestPayments extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_ledger_interest_payments` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `case_id` int(11) DEFAULT NULL,
			  `amount` decimal(12,2) DEFAULT NULL,
			  `date` date DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}
