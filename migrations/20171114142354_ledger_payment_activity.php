<?php

use \Console\Migration\BaseMigration;

class LedgerPaymentActivity extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_ledger_payment_activity` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `applied_payment_id` int(11) unsigned DEFAULT NULL,
			  `activity_date` datetime DEFAULT NULL,
			  `activity_user_id` int(11) unsigned DEFAULT NULL,
			  `date_of_payment` date DEFAULT NULL,
			  `patient_id` int(11) unsigned DEFAULT NULL,
			  `payment_source` tinyint(4) DEFAULT NULL,
			  `payment_method` tinyint(4) DEFAULT NULL,
			  `selected_patient_insurance_id` int(11) unsigned DEFAULT NULL,
			  `payment_amount` decimal(12,2) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");
    }
}
