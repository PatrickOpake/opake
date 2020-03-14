<?php

use \Console\Migration\BaseMigration;

class ClaimsProcessing extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `billing_navicure_payment` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `claim_id` int(11) unsigned DEFAULT NULL,
			  `payment_bunch_id` int(11) unsigned DEFAULT NULL,
			  `total_charge_amount` float DEFAULT NULL,
			  `total_allowed_amount` float DEFAULT NULL,
			  `total_payment` float DEFAULT NULL,
			  `provider_status_code` varchar(50) DEFAULT NULL,
			  `status` tinyint(11) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			);
		");

	    $this->query("
			CREATE TABLE `billing_navicure_payment_service` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `payment_id` int(11) unsigned DEFAULT NULL,
			  `hcpcs` varchar(50) DEFAULT NULL,
			  `quantity` int(11) DEFAULT NULL,
			  `charge_amount` float DEFAULT NULL,
			  `allowed_amount` float DEFAULT NULL,
			  `payment` float DEFAULT NULL,
			  `adjustment` float DEFAULT NULL,
			  `provider_status_code` varchar(10) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			);
	    ");

	    $this->query("
			CREATE TABLE `billing_navicure_payment_bunch` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `organization_id` int(11) unsigned DEFAULT NULL,
			  `payer_id` int(11) unsigned DEFAULT NULL,
			  `eft_date` date DEFAULT NULL,
			  `eft_number` varchar(50) DEFAULT NULL,
			  `total_amount` float DEFAULT NULL,
			  `amount_paid` float DEFAULT NULL,
			  `patient_responsible_amount` float DEFAULT NULL,
			  `status` tinyint(4) DEFAULT NULL,
			  PRIMARY KEY (`id`)
		  );
	    ");
    }
}
