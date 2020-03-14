<?php

use \Console\Migration\BaseMigration;

class InsurancePayorFill extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_payor`
				ADD `insurance_type` INT  NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `address` varchar(255) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `address2` varchar(255) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `country_id` int(11) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `state_id` int(11) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `custom_state` varchar(255) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `city_id` int(11) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `custom_city` varchar(255) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD  `phone` varchar(40) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `carrier_code` varchar(255) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `last_change_date` DATETIME NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `last_change_user_id` int(11) NULL DEFAULT NULL;
			ALTER TABLE `insurance_payor`
				ADD `zip_code` varchar(20) NULL DEFAULT NULL;
		");
    }
}
