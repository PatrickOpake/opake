<?php

use \Console\Migration\BaseMigration;

class ChatTable extends BaseMigration
{

	public function change()
	{
		$this->query("
		CREATE TABLE IF NOT EXISTS `chat_message` (
		`id` int(11) NOT NULL,
		  `organization_id` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  `date` datetime NOT NULL,
		  `text` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `chat_message` ADD PRIMARY KEY (`id`), ADD KEY `organization_id` (`organization_id`), ADD KEY `date` (`date`);
		ALTER TABLE `chat_message` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

		ALTER TABLE `user` ADD `chat_last_readed_id` INT NULL ;
        ");
	}
}
