<?php

use \Console\Migration\BaseMigration;

class PatientMrnCounter extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `patient_mrn_counter` (
                `organization_id` INT(10) NOT NULL,
                `counter` INT(10) NOT NULL,
                PRIMARY KEY (`organization_id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            ALTER TABLE `patient`
                CHANGE COLUMN `mrn` `mrn` INT(11) NULL DEFAULT NULL AFTER `language_id`,
                ADD COLUMN `mrn_year` INT(11) NULL DEFAULT NULL AFTER `mrn`;
        ");


        $this->query("
            UPDATE `patient` SET `mrn_year` = 16
        ");
    }
}
