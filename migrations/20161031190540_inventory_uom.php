<?php

use \Console\Migration\BaseMigration;

class InventoryUom extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `inventory`
		ADD `uom_id` INT NULL AFTER `manf_id`,
                DROP COLUMN `uom`;
        ");
    }
}
