<?php

use \Console\Migration\BaseMigration;

class AddFieldsToCaseBlocking extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_blocking` 
                ADD COLUMN `description` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `overwrite` tinyint(1) NOT NULL DEFAULT '0';
            
            ALTER TABLE `case_blocking_item` 
                ADD COLUMN `description` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `overwrite` tinyint(1) NOT NULL DEFAULT '0';

        ");
    }
}
