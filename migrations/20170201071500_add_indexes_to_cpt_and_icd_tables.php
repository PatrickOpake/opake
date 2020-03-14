<?php

use \Console\Migration\BaseMigration;

class AddIndexesToCptAndIcdTables extends BaseMigration
{
    public function change()
    {
        $this->query('
            ALTER TABLE `case_type` 
                CHANGE COLUMN `is_2016` `is_2016` TINYINT(1) NULL DEFAULT 0,
                ADD INDEX `IDX_code_name` (`code`, `name`);
                
            ALTER TABLE `icd` 
                CHANGE COLUMN `is_2016` `is_2016` TINYINT(1) NULL DEFAULT 0,
                ADD INDEX `IDX_code_desc` (`code`, `desc`);
        ');
    }
}
