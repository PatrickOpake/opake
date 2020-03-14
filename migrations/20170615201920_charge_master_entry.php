<?php

use \Console\Migration\BaseMigration;

class ChargeMasterEntry extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_coding_bill`
                ADD COLUMN `charge_master_entry_id` INT(11) NULL DEFAULT NULL AFTER `case_type_id`;
        ");
    }
}
