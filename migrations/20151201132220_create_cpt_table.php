<?php

use \Console\Migration\BaseMigration;

class CreateCptTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE IF NOT EXISTS `cpt` (
                `id` int(11) NOT NULL,
                `code` varchar(22) NOT NULL,
                `name` varchar(255) NOT NULL,
                `active` tinyint(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `cpt` ADD PRIMARY KEY (`id`);
            ALTER TABLE `cpt` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            INSERT INTO `cpt` (`code`, `name`, `active`)
                SELECT `code`, `name`, `active` from `case_type`;
        ");
	}
}
