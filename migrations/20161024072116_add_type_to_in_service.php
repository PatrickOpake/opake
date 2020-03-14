<?php

use \Console\Migration\BaseMigration;

class AddTypeToInService extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `case_in_service` ADD `type` INT(11) NULL DEFAULT NULL AFTER `location_id`;
    	");
    }
}
