<?php

use \Console\Migration\BaseMigration;

class UserCaseNotesTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE IF NOT EXISTS `user_case_note` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `last_read_note_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `user_case_note` ADD PRIMARY KEY (`id`);
            ALTER TABLE `user_case_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
