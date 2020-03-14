<?php

use \Console\Migration\BaseMigration;

class CaseInventoryItem extends BaseMigration
{
	public function change()
	{
		$this->query("
		CREATE TABLE IF NOT EXISTS `case_inventory_item` (
		`id` int(10) unsigned NOT NULL,
		  `case_id` int(11) NOT NULL,
		  `inventory_id` int(11) NOT NULL,
		  `quantity` int(11) NOT NULL
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

		ALTER TABLE `case_inventory_item` ADD PRIMARY KEY (`id`), ADD KEY `case_id` (`case_id`), ADD KEY `inventory_id` (`inventory_id`);
		ALTER TABLE `case_inventory_item` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
        ");
	}
}
