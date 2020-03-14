<?php

use \Console\Migration\BaseMigration;

class LedgerApplyingOptions extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_ledger_applying_options` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `coding_bill_id` int(11) unsigned DEFAULT NULL,
			  `is_force_patient_resp` tinyint(4) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}