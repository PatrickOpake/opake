<?php

use \Console\Migration\BaseMigration;

class AddNewFieldsInCaseTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case`
                ADD COLUMN `time_check_in` TIMESTAMP NULL AFTER `time_end`,
                ADD COLUMN `accompanied_by` TEXT NULL;
            
            CREATE TABLE `case_drivers_license` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `case_id` INT(10) NULL DEFAULT NULL,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `uploaded_date` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
            
            CREATE TABLE `case_insurance_card` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `case_id` INT(10) NULL DEFAULT NULL,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `uploaded_date` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
	}
}
