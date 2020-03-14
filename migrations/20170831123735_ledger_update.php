<?php

use \Console\Migration\BaseMigration;

class LedgerUpdate extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_ledger_applied_payment` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `coding_bill_id` int(11) unsigned DEFAULT NULL,
			  `payment_info_id` int(11) unsigned DEFAULT NULL,
			  `amount` float DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");

	    $this->query("
			CREATE TABLE `billing_ledger_payment_info` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `date_of_payment` date DEFAULT NULL,
			  `selected_patient_insurance_id` int(11) unsigned DEFAULT NULL,
			  `payment_source` tinyint(4) DEFAULT NULL,
			  `payment_method` tinyint(4) DEFAULT NULL,
			  `total_amount` float DEFAULT NULL,
			  `authorization_number` varchar(255) DEFAULT NULL,
			  `check_number` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
	    ");

	    $this->query("
	       DROP TABLE `billing_ledger_payment_activity`;
	       DROP TABLE `billing_ledger_last_applied_amount`;
	       DROP TABLE `billing_payment_posting_applied_payment`;
	       DROP TABLE `billing_payment_posting_applied_payment_note`;
	       DROP TABLE `billing_payment_posting_entered_insurance_payment`;
	       DROP TABLE `billing_payment_posting_entered_patient_payment`;
	    ");
    }
}
