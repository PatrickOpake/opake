<?php

use \Console\Migration\BaseMigration;

class AdjusmentsChange extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_navicure_payment_service` CHANGE `adjustment` `other_adjustments` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment_service` CHANGE `payment` `payment` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment_service` CHANGE `allowed_amount` `allowed_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment_service` CHANGE `charge_amount` `charge_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment_service` ADD `deduct_adjustments` DECIMAL(12,2)  NULL  DEFAULT NULL  AFTER `payment`;
			ALTER TABLE `billing_navicure_payment_service` ADD `co_pay_adjustments` DECIMAL(12,2)  NULL  DEFAULT NULL  AFTER `deduct_adjustments`;
			ALTER TABLE `billing_navicure_payment_service` ADD `co_ins_adjustments` DECIMAL(12,2)  NULL  DEFAULT NULL  AFTER `co_pay_adjustments`;
			ALTER TABLE `billing_navicure_payment_bunch` CHANGE `total_amount` `total_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment_bunch` CHANGE `amount_paid` `amount_paid` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment` CHANGE `total_payment` `total_payment` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment` CHANGE `total_allowed_amount` `total_allowed_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_navicure_payment` CHANGE `total_charge_amount` `total_charge_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
		");


	    $this->query("
	        CREATE TABLE `billing_navicure_payment_service_adjustment` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `payment_service_id` int(11) unsigned DEFAULT NULL,
			  `type` tinyint(4) DEFAULT NULL,
			  `amount` decimal(12,2) DEFAULT NULL,
			  `quantity` int(11) DEFAULT NULL,
			  `reason_code` varchar(10) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
	    ");
    }
}
