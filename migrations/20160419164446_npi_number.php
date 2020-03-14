<?php

use \Console\Migration\BaseMigration;

class NpiNumber extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user`
	          ADD COLUMN `npi_number` VARCHAR(255) NULL DEFAULT NULL AFTER `dashboard_group_type`;
        ");
    }
}
