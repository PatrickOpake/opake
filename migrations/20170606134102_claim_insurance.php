<?php

use \Console\Migration\BaseMigration;

class ClaimInsurance extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_navicure_claim_insurance_types` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `type` tinyint(4) DEFAULT NULL,
			  `order` tinyint(4) DEFAULT NULL,
			  `case_registration_insurance_id` int(11) DEFAULT NULL,
			  `insurance_data_id` int(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

	    $this->query("
	        ALTER TABLE `billing_navicure_claim` ADD
	        	`primary_insurance_id` INT  NULL  DEFAULT NULL  AFTER `last_transaction_date`;
	    ");
    }
}
