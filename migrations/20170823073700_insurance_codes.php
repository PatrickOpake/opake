<?php

use \Console\Migration\BaseMigration;

class InsuranceCodes extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_data_regular` ADD `ub04_payer_id` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `insurance_zip_code`;
			ALTER TABLE `insurance_data_regular` ADD `cms1500_payer_id` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `ub04_payer_id`;
			ALTER TABLE `insurance_data_regular` ADD `eligibility_payer_id` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `cms1500_payer_id`;
		");
    }
}
