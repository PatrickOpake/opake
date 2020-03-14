<?php

use \Console\Migration\BaseMigration;

class UserActivityTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE `user_activity` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `user_id` INT(10) NOT NULL,
                `date` DATETIME NOT NULL,
                `action` INT NOT NULL,
                `details` MEDIUMTEXT NULL DEFAULT NULL,
                `changes` MEDIUMTEXT NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_user_id` (`user_id`),
                INDEX `IDX_date` (`date`),
                INDEX `IDX_action` (`action`)
            )
            ENGINE=InnoDB;
        ");
	}
}
