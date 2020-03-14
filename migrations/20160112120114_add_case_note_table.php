<?php

use \Console\Migration\BaseMigration;

class AddCaseNoteTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE IF NOT EXISTS `case_note` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `time_add` timestamp NULL DEFAULT NULL,
                `text` TEXT DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_note` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
