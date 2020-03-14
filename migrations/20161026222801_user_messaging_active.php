<?php

use \Console\Migration\BaseMigration;

class UserMessagingActive extends BaseMigration
{
    public function change()
    {
	$this->query("
            ALTER TABLE `user` ADD `is_messaging_active` TINYINT NOT NULL DEFAULT '1' AFTER `is_scheduled_password_change`;
        ");
    }
}
