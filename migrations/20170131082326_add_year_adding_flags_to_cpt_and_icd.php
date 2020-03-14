<?php

use \Console\Migration\BaseMigration;

class AddYearAddingFlagsToCptAndIcd extends BaseMigration
{
    public function change()
    {
        $this->query('
            DELETE FROM `case_type` WHERE year_adding = 2017;
            ALTER TABLE `case_type` DROP COLUMN `year_adding`,
                ADD COLUMN `is_2016` TINYINT(1) NULL DEFAULT 1,
                ADD COLUMN `is_2017` TINYINT(1) NULL DEFAULT 0;
                
            DELETE FROM `icd` WHERE year_adding = 2017;
            ALTER TABLE `icd` DROP COLUMN `year_adding`,
                ADD COLUMN `is_2016` TINYINT(1) NULL DEFAULT 1,
                ADD COLUMN `is_2017` TINYINT(1) NULL DEFAULT 0;
        ');
    }
}
