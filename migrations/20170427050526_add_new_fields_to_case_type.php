<?php

use \Console\Migration\BaseMigration;

class AddNewFieldsToCaseType extends BaseMigration
{
    public function change()
    {
        $this->query("
	        ALTER TABLE `case_type`
	            ADD COLUMN `archived` TINYINT(1) NULL DEFAULT 0,
	            ADD COLUMN `last_update` TINYINT(1) NULL DEFAULT 0;
        ");
    }
}
