<?php

use \Console\Migration\BaseMigration;

class PatientUserHints extends BaseMigration
{

    public function change()
    {
        $this->query("
            CREATE TABLE `patient_user_hints` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `patient_user_id` INT(10) NOT NULL,
                `is_appointments_hint_hidden` TINYINT NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
