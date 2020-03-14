<?php

use \Console\Migration\BaseMigration;

class PatientAppointmentAlertHidden extends BaseMigration
{
    public function change()
    {
        $this->query("
             CREATE TABLE `patient_user_appointment_alert_hidden` (
                    `id` INT(10) NULL AUTO_INCREMENT,
                    `patient_user_id` INT(10) NOT NULL,
                    `case_registration_id` INT(10) NOT NULL,
                    `is_info_alert_hidden` TINYINT NOT NULL DEFAULT '0',
                    `is_insurance_alert_hidden` TINYINT NOT NULL DEFAULT '0',
                    `is_forms_alert_hidden` TINYINT NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                )
              ENGINE=InnoDB;
        ");


    }
}
