<?php

use \Console\Migration\BaseMigration;

class AddCaseColoToPracticeGroup extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `organization_practice_groups` ADD `case_color` VARCHAR(255) NULL DEFAULT NULL;
    	");
    }
}
