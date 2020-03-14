<?php

use \Console\Migration\BaseMigration;

class SessionTables extends BaseMigration
{
    public function change()
    {
        try {
            $this->query("
              CREATE TABLE `user_session` (
                    `id` INT(10) NOT NULL AUTO_INCREMENT,
                    `user_id` INT(10) NULL DEFAULT NULL,
                    `hash` VARCHAR(50) NULL DEFAULT NULL,
                    `started` DATETIME NULL DEFAULT NULL,
                    `expired` DATETIME NULL DEFAULT NULL,
                    `is_remember_me` TINYINT(1) NOT NULL DEFAULT '0',
                    `active` TINYINT(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    UNIQUE INDEX `IDX_hash` (`hash`)
                )
                ENGINE=InnoDB;
            ");
        } catch (\Exception $e) {

        }

        try {

            $this->query("
                CREATE TABLE `patient_user_session` (
                    `id` INT(10) NOT NULL AUTO_INCREMENT,
                    `patient_user_id` INT(10) NULL DEFAULT NULL,
                    `hash` VARCHAR(50) NULL DEFAULT NULL,
                    `started` DATETIME NULL DEFAULT NULL,
                    `expired` DATETIME NULL DEFAULT NULL,
                    `is_remember_me` TINYINT(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    UNIQUE INDEX `IDX_hash` (`hash`)
                )
                ENGINE=InnoDB;
          ");

        } catch (\Exception $e) {

        }



    }
}
