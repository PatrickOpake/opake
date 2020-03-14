<?php

use \Console\Migration\BaseMigration;

class CaseColorForLocations extends BaseMigration
{

    public function change()
    {
        $this->query("
            ALTER TABLE `location` ADD `case_color` VARCHAR(255) NULL ;
        ");
    }
}
