<?php

use \Console\Migration\BaseMigration;

class PrefCardStage extends BaseMigration
{
    public function change()
    {
        $this->query("
		CREATE TABLE IF NOT EXISTS `pref_card_stage` (
		  `id` int(11) NOT NULL,
		  `name` varchar(255) DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `pref_card_stage` ADD PRIMARY KEY (`id`);
		ALTER TABLE `pref_card_stage` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
    	");
    }
}
