<?php

use \Console\Migration\BaseMigration;

class AddPositionToPrefCardItem extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `pref_card_staff_item` ADD `position` INT(11) NULL DEFAULT NULL;
    		ALTER TABLE `card_staff_item` ADD `position` INT(11) NULL DEFAULT NULL;
    	");
    }
}
