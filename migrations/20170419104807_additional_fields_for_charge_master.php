<?php

use \Console\Migration\BaseMigration;

class AdditionalFieldsForChargeMaster extends BaseMigration
{
    public function change()
    {
        $this->query("
	        ALTER TABLE `master_charge`
	            ADD COLUMN `notes` VARCHAR(255) NULL DEFAULT NULL,
	            ADD COLUMN `last_edited_date` DATETIME NULL DEFAULT NULL,
	            ADD COLUMN `historical_price` VARCHAR(255) NULL DEFAULT NULL;
        ");
    }
}
