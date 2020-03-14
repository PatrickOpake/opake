<?php

use \Console\Migration\BaseMigration;

class BillingReports extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `billing_cases_report` (
                `id` INT(11) AUTO_INCREMENT,
                `case_id` INT(11) NULL DEFAULT NULL,
                `at_top` TINYINT(1) NULL DEFAULT 0,
                `dos` DATE NULL DEFAULT NULL,
                `last_name` VARCHAR(255) NULL,
                `first_name` VARCHAR(255) NULL,
                `id_number` VARCHAR(255) NULL,
                `doctor` VARCHAR(255) NULL,
                `insurance_modifiers` VARCHAR(255) NULL,
                `insurance` VARCHAR(255) NULL,
                `prefix` VARCHAR(255) NULL,
                `cd` VARCHAR(255) NULL,
                `cpt` VARCHAR(255) NULL,
                `charges` DECIMAL(10,2) DEFAULT NULL,
                `recent_payment` DATE NULL DEFAULT NULL,
                `pmt` DECIMAL(10,2) DEFAULT NULL,
                `ins_adj` DECIMAL(10,2) DEFAULT NULL,
                `bs` VARCHAR(255) NULL,
                `deductible` DECIMAL(10,2) DEFAULT NULL,
                `co_pay` DECIMAL(10,2) DEFAULT NULL,
                `tfr_prov` DECIMAL(10,2) DEFAULT NULL,
                `balance` DECIMAL(10,2) DEFAULT NULL,
                `var_cost` DECIMAL(10,2) DEFAULT NULL,
                `or_time` DECIMAL(10,2) DEFAULT NULL,
                `notes` VARCHAR(255) NULL,
                PRIMARY KEY (`id`)
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	         
            CREATE TABLE `billing_procedures_report` (
                `id` INT(11) AUTO_INCREMENT,
                `case_id` INT(11) NULL DEFAULT NULL,
                `at_top` TINYINT(1) NULL DEFAULT 0,
                `dos` DATE NULL DEFAULT NULL,
                `last_name` VARCHAR(255) NULL,
                `first_name` VARCHAR(255) NULL,
                `id_number` VARCHAR(255) NULL,
                `location` VARCHAR(255) NULL,
                `fee_type` VARCHAR(255) NULL,
                `cpt` VARCHAR(255) NULL,
                `fee` DECIMAL(10,2) DEFAULT NULL,
                `pn1` VARCHAR(255) NULL,
                `dr_id` VARCHAR(255) NULL,
                `ins1` VARCHAR(255) NULL,
                `normalized_ins_id` VARCHAR(255) NULL,
                `insurance_name` VARCHAR(255) NULL,
                PRIMARY KEY (`id`)
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	        
	        INSERT INTO `billing_cases_report` (case_id) SELECT id FROM `case`;
	        INSERT INTO `billing_procedures_report` (case_id) SELECT id FROM `case`;        
        ");
    }
}
