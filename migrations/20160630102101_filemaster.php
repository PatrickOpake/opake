<?php

use \Console\Migration\BaseMigration;

class Filemaster extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `patient_chart` (
                `id` INT NULL AUTO_INCREMENT,
                `patient_id` INT NULL,
                `name` VARCHAR(255) NULL DEFAULT NULL,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `remote_file_id` INT(10) NULL DEFAULT NULL,
                `uploaded_date` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `case_chart` (
                `id` INT NULL AUTO_INCREMENT,
                `case_id` INT NULL,
                `name` VARCHAR(255) NULL DEFAULT NULL,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `remote_file_id` INT(10) NULL DEFAULT NULL,
                `uploaded_date` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
