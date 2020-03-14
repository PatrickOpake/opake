<?php

use \Console\Migration\BaseMigration;

class InsurancePaymentPosting extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_payment_posting_entered_insurance_payment` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `payment_id_text` varchar(55) DEFAULT NULL,
			  `date_paid` date DEFAULT NULL,
			  `insurance_payer_id` int(11) unsigned DEFAULT NULL,
			  `insurance_code` varchar(55) DEFAULT NULL,
			  `payment_type` tinyint(4) DEFAULT NULL,
			  `authorization_number` varchar(255) DEFAULT NULL,
			  `check_number` varchar(255) DEFAULT NULL,
			  `description` varchar(1024) DEFAULT NULL,
			  `total_amount_paid` float DEFAULT NULL,
			  `unapplied_amount` float DEFAULT NULL,
			  `applied_amount` float DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
		");

    }
}
