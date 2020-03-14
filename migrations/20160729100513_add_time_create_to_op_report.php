<?php

use \Console\Migration\BaseMigration;

class AddTimeCreateToOpReport extends BaseMigration
{
    public function change()
    {
        $this->query("       
            ALTER TABLE `case_op_report` ADD COLUMN `time_start` DATETIME NULL DEFAULT NULL AFTER `type`;
        ");
    }
}
