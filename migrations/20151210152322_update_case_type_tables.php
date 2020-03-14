<?php

use \Console\Migration\BaseMigration;

class UpdateCaseTypeTables extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case_cpt`
                ADD UNIQUE KEY `uni` (`case_id`,`cpt_id`),
                DROP COLUMN `id`;
            ALTER TABLE `case_type_cpt`
                ADD UNIQUE KEY `uni` (`case_type_id`,`cpt_id`),
                DROP COLUMN `id`;
        ");
	}
}
