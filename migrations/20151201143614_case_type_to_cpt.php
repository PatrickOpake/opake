<?php

use \Console\Migration\BaseMigration;

class CaseTypeToCpt extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE IF NOT EXISTS `case_type_cpt` (
                `id` int(11) NOT NULL,
                `case_type_id` int(11) NOT NULL,
                `cpt_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_type_cpt` 
            ADD PRIMARY KEY (`id`),
            ADD KEY `case_type_id` (`case_type_id`),
            ADD KEY `cpt_id` (`cpt_id`);
            ALTER TABLE `case_type_cpt` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
