<?php

use \Console\Migration\BaseMigration;

class PrefCardItemsStagesSorting extends BaseMigration
{

    public function change()
    {
        $this->query("ALTER TABLE `pref_card_staff` CHANGE `stages` `stages` TEXT NULL DEFAULT NULL;");
    }
}
