<?php

use \Console\Migration\BaseMigration;

class AddCaseNotesCountColumn extends BaseMigration
{
	public function change()
	{
		$this->query("
            DELETE FROM `case_note`;
            ALTER TABLE `case_note` ADD INDEX(`case_id`);
            ALTER TABLE `case` ADD COLUMN `notes_count` INT(11) NOT NULL DEFAULT 0;
        ");
	}
}
