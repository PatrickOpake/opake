<?php

use \Console\Migration\BaseMigration;

class PaymentPosting extends BaseMigration
{

    public function change()
    {
		$this->query("
			CREATE TABLE `billing_payment_posting_entered_patient_payment` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `patient_id` int(11) DEFAULT NULL,
			  `date_paid` date DEFAULT NULL,
			  `amount_paid` float DEFAULT NULL,
			  `payment_type` tinyint(4) DEFAULT NULL,
			  `is_co_pay` tinyint(4) NOT NULL DEFAULT '0',
			  `authorization_number` varchar(150) DEFAULT NULL,
			  `check_number` varchar(150) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");

	    $this->query("
			CREATE TABLE `billing_payment_posting_applied_payment` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `coding_bill_id` int(11) DEFAULT NULL,
			  `is_co_pay` tinyint(4) NOT NULL DEFAULT '0',
			  `amount_posted` float DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
	    ");
    }
}
