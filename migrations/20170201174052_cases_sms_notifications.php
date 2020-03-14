<?php

use \Console\Migration\BaseMigration;

class CasesSmsNotifications extends BaseMigration
{
    public function change()
    {
        $this->query('
		ALTER TABLE `case` ADD COLUMN `is_sms_reminded` TINYINT(1) NULL DEFAULT 0 AFTER `notes_count`;

		CREATE TABLE IF NOT EXISTS `case_sms_log` (
		  `id` int(10) unsigned NOT NULL,
		  `message_sid` varchar(34) DEFAULT NULL,
		  `case_id` int(10) unsigned NOT NULL,
		  `type` tinyint(4) NOT NULL,
		  `phone_to` varchar(12) NOT NULL,
		  `status` tinyint(4) NOT NULL,
		  `send_date` datetime NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `case_sms_log` ADD PRIMARY KEY (`id`), ADD KEY `case_id` (`case_id`);

		ALTER TABLE `case_sms_log` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
        ');
    }
}
