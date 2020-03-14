<?php

use \Console\Migration\BaseMigration;

class CaseToCpt extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE IF NOT EXISTS `case_cpt` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `cpt_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_cpt` 
            ADD PRIMARY KEY (`id`),
            ADD KEY `case_id` (`case_id`),
            ADD KEY `cpt_id` (`cpt_id`);
            ALTER TABLE `case_cpt` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
