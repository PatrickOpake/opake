<?php

use \Console\Migration\BaseMigration;

class AddCptIdToCaseType extends BaseMigration
{

    public function change()
    {
        $this->query("
	        ALTER TABLE `case_type` ADD COLUMN `cpt_id` INT(50) NULL DEFAULT NULL AFTER `organization_id`;
        ");
    }
}
