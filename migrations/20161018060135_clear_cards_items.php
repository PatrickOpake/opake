<?php

use \Console\Migration\BaseMigration;

class ClearCardsItems extends BaseMigration
{
    public function change()
    {
        $this->query("
            DELETE FROM `card_staff_item` WHERE inventory_id IS NULL OR inventory_id = 0;
            ALTER TABLE `card_staff_item` DROP COLUMN `item_number`;
            
            DELETE FROM `pref_card_staff_item` WHERE inventory_id IS NULL OR inventory_id = 0;
            ALTER TABLE `pref_card_staff_item` DROP COLUMN `item_number`;
        ");
    }
}
