<?php

use \Console\Migration\BaseMigration;

class SmsLogUpdates extends BaseMigration
{
    public function change()
    {
	$this->query("
		ALTER TABLE `case` DROP `is_sms_reminded`;

		RENAME TABLE `case_sms_log` TO `sms_log`;

		CREATE TABLE IF NOT EXISTS `case_sms_log` (
		  `id` int(10) unsigned NOT NULL,
		  `case_id` int(10) unsigned NOT NULL,
		  `sms_log_id` int(10) unsigned NOT NULL,
		  `type` tinyint(3) unsigned NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		ALTER TABLE `case_sms_log` ADD PRIMARY KEY (`id`), ADD KEY `case_id` (`case_id`);
		ALTER TABLE `case_sms_log` MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

		INSERT INTO `case_sms_log`(`case_id`, `sms_log_id`, `type`) SELECT `case_id`, `id`, `type` FROM `sms_log`;

		ALTER TABLE `sms_log` DROP `case_id`;
		ALTER TABLE `sms_log` DROP `type`;
		ALTER TABLE `sms_log` ADD `body` TEXT NOT NULL AFTER `phone_to`;
		ALTER TABLE `sms_log` ADD INDEX(`send_date`);
	");
    }
}
