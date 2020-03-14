<?php

use \Console\Migration\BaseMigration;

class CodingInsuranceAddFields extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `case_coding_insurance` ADD `state_id` INT(11) UNSIGNED  NULL  DEFAULT NULL;
			ALTER TABLE `case_coding_insurance` ADD `city_id` INT(11) UNSIGNED  NULL  DEFAULT NULL;
			ALTER TABLE `case_coding_insurance` ADD `zip_code` VARCHAR(25)  NULL  DEFAULT NULL;
		");
    }
}
