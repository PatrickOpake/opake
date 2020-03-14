<?php

use \Console\Migration\BaseMigration;

class PrefCardsCleanup extends BaseMigration
{
    public function change()
    {
        $this->query("
          UPDATE `pref_card_staff` SET `stages`=NULL;
          ALTER TABLE `pref_card_staff_item` DROP COLUMN `actual_use`;

          DROP TABLE `card_location`;
          DROP TABLE `card_location_item`;
          DROP TABLE `card_location_note`;

          DROP TABLE `pref_card_location`;
          DROP TABLE `pref_card_location_item`;
          DROP TABLE `pref_card_location_note`;
        ");
    }
}
