<?php

use \Console\Migration\BaseMigration;

class PatientPortal extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `patient_portal` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `organization_id` INT(10) NOT NULL,
                `title` VARCHAR(255) NULL DEFAULT NULL,
                `alias` VARCHAR(255) NULL DEFAULT NULL,
                `active` TINYINT NULL DEFAULT '0',
                `icon_file_id` INT NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `patient_user` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `patient_id` INT(10) NULL DEFAULT NULL,
                `password` VARCHAR(100) NULL DEFAULT NULL,
                `hash` VARCHAR(100) NULL DEFAULT NULL,
                `active` TINYINT NOT NULL DEFAULT '0',
                `created` DATETIME NULL,
                `photo_id` INT NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
