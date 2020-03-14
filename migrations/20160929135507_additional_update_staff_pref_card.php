<?php

use \Console\Migration\BaseMigration;

class AdditionalUpdateStaffPrefCard extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `pref_card_staff_item` CHANGE `inventory_id` `inventory_id` INT(11) NULL DEFAULT NULL;
    	");
    }
}
