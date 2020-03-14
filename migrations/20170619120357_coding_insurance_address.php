<?php

use \Console\Migration\BaseMigration;

class CodingInsuranceAddress extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `case_coding_insurance`
				ADD `address` VARCHAR(1024)  NULL  DEFAULT NULL;
		");

	    $this->query("
			ALTER TABLE `case_coding_insurance`
				ADD `selected_case_insurance_id` INT  NULL  DEFAULT NULL  AFTER `coding_id`;
	    ");

	    $this->query("
	        ALTER TABLE `case_registration_insurance_types`
	        	ADD `deleted` TINYINT(4) NOT NULL DEFAULT '0' AFTER `insurance_data_id`;
	    ");
    }
}
