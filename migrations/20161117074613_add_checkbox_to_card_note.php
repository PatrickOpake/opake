<?php

use \Console\Migration\BaseMigration;

class AddCheckboxToCardNote extends BaseMigration
{
    public function change()
    {
        $this->query("
			ALTER TABLE `card_staff_note` ADD `is_checked` TINYINT(4) NULL DEFAULT NULL;
		");
    }
}
