<?php

use \Console\Migration\BaseMigration;

class Documents extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case_hp`
                ADD COLUMN `uploaded_file_id` INT(11) NOT NULL AFTER `case_id`,
                DROP COLUMN `path`;
        ");

		$this->query("
            ALTER TABLE `case_discharge`
                ADD COLUMN `uploaded_file_id` INT(11) NOT NULL AFTER `case_id`,
                DROP COLUMN `path`;
        ");
	}
}
