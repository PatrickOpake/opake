<?php

use \Console\Migration\BaseMigration;

class AddChargeableToOrganization extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `organization` ADD `chargeable` FLOAT NULL DEFAULT NULL;
    	");
    }
}
