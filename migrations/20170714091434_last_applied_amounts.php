<?php

use \Console\Migration\BaseMigration;

class LastAppliedAmounts extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_ledger_last_applied_amount` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `patient_id` int(11) unsigned DEFAULT NULL,
			  `insurance_payments_amount` float DEFAULT NULL,
			  `patient_payments_amount` float DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}