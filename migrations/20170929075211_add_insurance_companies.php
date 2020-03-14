<?php

use \Console\Migration\BaseMigration;

class AddInsuranceCompanies extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_data_auto_accident` ADD `insurance_company_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL AFTER `id`;
			ALTER TABLE `insurance_data_auto_accident` ADD `selected_insurance_company_address_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `eligibility_payer_id`;
		");

	    $this->query("
			ALTER TABLE `insurance_data_workers_comp` ADD `insurance_company_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL AFTER `id`;
			ALTER TABLE `insurance_data_workers_comp` ADD `selected_insurance_company_address_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `eligibility_payer_id`;
	    ");
    }
}
