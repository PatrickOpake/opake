<?php

use \Console\Migration\BaseMigration;

class AddDashboardGroupByTypeToUser extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user` ADD `dashboard_group_type` VARCHAR(20) NOT NULL DEFAULT 'surgeon';
        ");
    }
}
