<?php

use \Console\Migration\BaseMigration;

class HcpcAbu extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `hcpc` ADD `abu` INT NULL;
    	");
    }
}
