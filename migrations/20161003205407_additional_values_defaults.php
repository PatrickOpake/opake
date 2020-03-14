<?php

use \Console\Migration\BaseMigration;

class AdditionalValuesDefaults extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `booking_additional_type`
           		CHANGE COLUMN `order` `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `booking_id`;
			ALTER TABLE `case_additional_type`
				CHANGE COLUMN `order` `order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `case_id`;
        ");
    }
}
