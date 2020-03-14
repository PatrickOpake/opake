<?php

use \Console\Migration\BaseMigration;

class PatientAppointmentForms extends BaseMigration
{
    public function change()
    {
        $this->query("
          CREATE TABLE `patient_appointment_form_pre_operative` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `case_registration_id` INT(10) NULL DEFAULT NULL,
                `filled_date` DATETIME NULL DEFAULT NULL,
                `height_ft` INT(11) NULL DEFAULT NULL,
                `height_in` INT(11) NULL DEFAULT NULL,
                `weight_lbs` INT(11) NULL DEFAULT NULL,
                `smoke_how_long_yrs` INT(11) NULL DEFAULT NULL,
                `smoke_packs_per_day` INT(11) NULL DEFAULT NULL,
                `drink_how_long_yrs` INT(11) NULL DEFAULT NULL,
                `drink_drinks_per_week` INT(11) NULL DEFAULT NULL,
                `medications` MEDIUMTEXT NULL,
                `steroids` MEDIUMTEXT NULL,
                `allergies` MEDIUMTEXT NULL,
                `surgeries_hospitalizations` MEDIUMTEXT NULL,
                `family_problems` MEDIUMTEXT NULL,
                `family_anesthesia_problems` MEDIUMTEXT NULL,
                `conditions` MEDIUMTEXT NULL,
                `allergic_to_latex` TINYINT(1) NULL DEFAULT NULL,
                `allergic_to_food` TINYINT(1) NULL DEFAULT NULL,
                `allergic_other` TINYINT(1) NULL DEFAULT NULL,
                `smoke` TINYINT(1) NULL DEFAULT NULL,
                `drink` TINYINT(1) NULL DEFAULT NULL,
                `living_will` TINYINT(1) NULL DEFAULT NULL,
                `leave_message` TINYINT(1) NULL DEFAULT NULL,
                `allergic_to_latex_reason` VARCHAR(255) NULL DEFAULT NULL,
                `allergic_to_food_reason` VARCHAR(255) NULL DEFAULT NULL,
                `allergic_other_reason` VARCHAR(255) NULL DEFAULT NULL,
                `drink_description` VARCHAR(255) NULL DEFAULT NULL,
                `travel_outside` VARCHAR(255) NULL DEFAULT NULL,
                `primary_care_name` VARCHAR(255) NULL DEFAULT NULL,
                `caretaker_name` VARCHAR(255) NULL DEFAULT NULL,
                `transportation_name` VARCHAR(255) NULL DEFAULT NULL,
                `smoke_description` VARCHAR(255) NULL DEFAULT NULL,
                `primary_care_phone` VARCHAR(20) NULL DEFAULT NULL,
                `caretaker_phone` VARCHAR(20) NULL DEFAULT NULL,
                `leave_message_phone` VARCHAR(20) NULL DEFAULT NULL,
                `transportation_phone` VARCHAR(20) NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_case_registration_id` (`case_registration_id`)
            )
            ENGINE=InnoDB;
        ");

         $this->query("
            CREATE TABLE `patient_appointment_form_influenza` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `case_registration_id` INT(10) NULL DEFAULT NULL,
                `filled_date` DATETIME NULL DEFAULT NULL,
                `travel_outside` TINYINT(1) NULL DEFAULT NULL,
                `flu_vaccine` TINYINT(1) NULL DEFAULT NULL,
                `flu_vaccine_month` INT NULL DEFAULT NULL,
                `travel_outside_date` DATE NULL DEFAULT NULL,
                `illnesses` MEDIUMTEXT NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_case_registration_id` (`case_registration_id`)
            )
            ENGINE=InnoDB;
         ");
    }
}
