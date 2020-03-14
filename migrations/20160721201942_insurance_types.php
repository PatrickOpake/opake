<?php

use \Console\Migration\BaseMigration;

class InsuranceTypes extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `insurance_data_auto_accident` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `insurance_name` VARCHAR(255) NULL DEFAULT NULL,
                `adjuster_name` VARCHAR(255) NULL DEFAULT NULL,
                `claim` VARCHAR(255) NULL DEFAULT NULL,
                `adjuster_phone` VARCHAR(40) NULL DEFAULT NULL,
                `insurance_address` VARCHAR(255) NULL DEFAULT NULL,
                `city_id` INT(11) NULL DEFAULT NULL,
                `state_id` INT(11) NULL DEFAULT NULL,
                `zip` VARCHAR(20) NULL DEFAULT NULL,
                `accident_date` DATE NULL DEFAULT NULL,
                `attorney_name` VARCHAR(255) NULL DEFAULT NULL,
                `attorney_phone` VARCHAR(40) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `insurance_data_workers_comp` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `insurance_name` VARCHAR(255) NULL DEFAULT NULL,
                `adjuster_name` VARCHAR(255) NULL DEFAULT NULL,
                `claim` VARCHAR(255) NULL DEFAULT NULL,
                `adjuster_phone` VARCHAR(40) NULL DEFAULT NULL,
                `insurance_address` VARCHAR(255) NULL DEFAULT NULL,
                `city_id` INT(11) NULL DEFAULT NULL,
                `state_id` INT(11) NULL DEFAULT NULL,
                `zip` VARCHAR(20) NULL DEFAULT NULL,
                `accident_date` DATE NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("

            CREATE TABLE `insurance_data_regular` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `insurance_id` INT(11) NULL DEFAULT NULL,
                `last_name` VARCHAR(255) NULL DEFAULT NULL,
                `first_name` VARCHAR(255) NULL DEFAULT NULL,
                `middle_name` VARCHAR(255) NULL DEFAULT NULL,
                `suffix` TINYINT(4) NULL DEFAULT NULL,
                `dob` DATE NULL DEFAULT NULL,
                `gender` TINYINT(4) NULL DEFAULT NULL,
                `phone` VARCHAR(40) NULL DEFAULT NULL,
                `address` VARCHAR(255) NULL DEFAULT NULL,
                `apt_number` VARCHAR(255) NULL DEFAULT NULL,
                `country_id` INT(11) NULL DEFAULT NULL,
                `state_id` INT(11) NULL DEFAULT NULL,
                `custom_state` VARCHAR(255) NULL DEFAULT NULL,
                `city_id` INT(11) NULL DEFAULT NULL,
                `custom_city` VARCHAR(255) NULL DEFAULT NULL,
                `zip_code` VARCHAR(20) NULL DEFAULT NULL,
                `relationship_to_insured` TINYINT(4) NULL DEFAULT NULL,
                `type` TINYINT(4) NULL DEFAULT NULL,
                `policy_number` VARCHAR(40) NULL DEFAULT NULL,
                `group_number` VARCHAR(40) NULL DEFAULT NULL,
                `order` TINYINT(1) NULL DEFAULT NULL,
                `provider_phone` VARCHAR(40) NULL DEFAULT NULL,
                `insurance_verified` TINYINT(4) NULL DEFAULT '0',
                `is_pre_authorization_completed` TINYINT(4) NULL DEFAULT '0',
                `address_insurance` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `case_registration_insurance_types` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `registration_id` INT(10) NULL,
                `type` INT(10) NULL,
                `order` INT(10) NULL,
                `selected_insurance_id` INT(10) NULL,
                `insurance_data_id` INT(10) NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `patient_insurance_types` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `patient_id` INT(10) NULL,
                `type` INT(10) NULL,
                `order` INT(10) NULL,
                `insurance_data_id` INT(10) NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `booking_insurance_types` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `booking_id` INT(10) NULL,
                `type` INT(10) NULL,
                `order` INT(10) NULL,
                 `selected_insurance_id` INT(10) NULL,
                `insurance_data_id` INT(10) NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
