<?php

use \Console\Migration\BaseMigration;

class PrefCardIds extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `pref_card_staff_item`
                ADD COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
                ADD PRIMARY KEY (`id`);
        ");
		$this->query("
            ALTER TABLE `pref_card_staff_note`
                ADD COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
                ADD PRIMARY KEY (`id`);
        ");
		$this->query("
            ALTER TABLE `pref_card_location_item`
                ADD COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
                ADD PRIMARY KEY (`id`);
        ");
		$this->query("
            ALTER TABLE `pref_card_location_note`
                ADD COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
                ADD PRIMARY KEY (`id`);
        ");
	}
}
