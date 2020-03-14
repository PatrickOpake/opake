<?php

use \Console\Migration\BaseMigration;

class InsuranceCompanyName extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_data_regular` ADD `insurance_company_name` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `insurance_id`;
		");

	    $this->query("
			ALTER TABLE `insurance_data_auto_accident` ADD `ub04_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
			ALTER TABLE `insurance_data_auto_accident` ADD `cms1500_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
			ALTER TABLE `insurance_data_auto_accident` ADD `eligibility_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
		");

	    $this->query("
			ALTER TABLE `insurance_data_workers_comp` ADD `ub04_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
			ALTER TABLE `insurance_data_workers_comp` ADD `cms1500_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
			ALTER TABLE `insurance_data_workers_comp` ADD `eligibility_payer_id` VARCHAR(255)  NULL  DEFAULT NULL;
		");

    }
}
