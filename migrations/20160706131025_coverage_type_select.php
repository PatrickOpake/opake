<?php

use \Console\Migration\BaseMigration;

class CoverageTypeSelect extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient`
                CHANGE COLUMN `coverage_type` `coverage_type_custom` VARCHAR(255) NULL DEFAULT NULL AFTER `photo_id`,
                ADD COLUMN `coverage_type` INT NULL DEFAULT '0' AFTER `status`;
        ");

        $this->query("
            ALTER TABLE `case_registration`
                CHANGE COLUMN `coverage_type` `coverage_type_custom` VARCHAR(255) NULL DEFAULT NULL AFTER `auto_is_primary`,
                ADD COLUMN `coverage_type` INT NULL DEFAULT '0' AFTER `family_out_of_pocket_maximum`;
        ");
    }
}
