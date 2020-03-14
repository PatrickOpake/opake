<?php

use \Console\Migration\BaseMigration;

class PatientAppointmentsConfirm extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `patient_user_appointment_confirm` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `patient_user_id` INT(10) NOT NULL,
                `case_registration_id` INT(10) NOT NULL,
                `is_patient_info_confirmed` TINYINT NOT NULL DEFAULT '0',
                `is_insurances_confirmed` TINYINT NOT NULL DEFAULT '0',
                `is_forms_confirmed` TINYINT NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
