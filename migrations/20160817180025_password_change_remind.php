<?php

use \Console\Migration\BaseMigration;

class PasswordChangeRemind extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `user_last_passwords` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `user_id` INT(10) NULL DEFAULT NULL,
                `password_hash` VARCHAR(100) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            ALTER TABLE `user`
                ADD COLUMN `last_password_change_date` DATETIME NULL DEFAULT NULL AFTER `time_status_change`;
        ");

        $this->query("
            ALTER TABLE `user`
                ADD COLUMN `is_scheduled_password_change` TINYINT(4) NOT NULL DEFAULT '0' AFTER `is_temp_password`;
        ");

        $now = new \DateTime();
        $this->getDb()->query('update')
            ->table('user')
            ->data([
                'last_password_change_date' => $now->format('Y-m-d H:i:s')
            ])
            ->execute();


    }
}
