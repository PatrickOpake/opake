<?php

use \Console\Migration\BaseMigration;

class InsuranceRegularFields extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_data_regular` ADD `insurance_state_id` INT  UNSIGNED  NULL  DEFAULT NULL;
			ALTER TABLE `insurance_data_regular` ADD `insurance_city_id` INT  UNSIGNED  NULL  DEFAULT NULL;
			ALTER TABLE `insurance_data_regular` ADD `insurance_zip_code` VARCHAR(20)  NULL  DEFAULT NULL;
		");
    }
}
