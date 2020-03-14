<?php

use \Console\Migration\BaseMigration;

class InServiceComments extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `in_service_note` (
                `id` int(11) NOT NULL,
                `in_service_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `time_add` datetime DEFAULT NULL,
                `text` text
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;            
            ALTER TABLE `in_service_note` ADD PRIMARY KEY (`id`), ADD KEY `in_service_id` (`in_service_id`);            
            ALTER TABLE `in_service_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            CREATE TABLE `user_in_service_note` (
                `id` int(11) NOT NULL,
                `in_service_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `last_read_note_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `user_in_service_note` ADD PRIMARY KEY (`id`);            
            ALTER TABLE `user_in_service_note` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            ALTER TABLE `case_in_service` ADD COLUMN `notes_count` INT(11) NOT NULL DEFAULT 0;
        ");
    }
}
