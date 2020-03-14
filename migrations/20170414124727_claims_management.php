<?php

use \Console\Migration\BaseMigration;

class ClaimsManagement extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_navicure_claim` ADD `last_transaction_date` DATETIME  NULL  DEFAULT NULL  AFTER `last_update`;
			ALTER TABLE `billing_navicure_claim` ADD `first_name` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `case_id`;
			ALTER TABLE `billing_navicure_claim` ADD `last_name` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `first_name`;
			ALTER TABLE `billing_navicure_claim` ADD `mrn` VARCHAR(30)  NULL  DEFAULT NULL  AFTER `last_name`;
			ALTER TABLE `billing_navicure_claim` ADD `dos` DATETIME  NULL  DEFAULT NULL  AFTER `mrn`;
			ALTER TABLE `billing_navicure_claim` ADD `insurance_payer_id` INT  NULL  DEFAULT NULL  AFTER `dos`;
		");
    }
}
