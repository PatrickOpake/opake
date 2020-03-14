<?php

use \Console\Migration\BaseMigration;

class MedicationReconciliation extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `case_registration_reconciliation` (
                `id` INT(10) AUTO_INCREMENT,
                `registration_id` INT(10) NOT NULL,
                `no_known_allergies` tinyint(1) NULL DEFAULT '0',
                `copy_given_to_patient` tinyint(1) NULL DEFAULT '0',
                `patient_denies` tinyint(1) NULL DEFAULT '0',
                `pre_op_call` tinyint(1) NULL DEFAULT '0',
                `admission` tinyint(1) NULL DEFAULT '0',
                `anesthesia_type` int(11) NULL DEFAULT NULL,
                `anesthesia_drugs` int(11) NULL DEFAULT NULL,
                `anesthesia_drugs_other` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `reconciliation_allergy` (
                `id` INT(10) AUTO_INCREMENT,
                `reconciliation_id` INT(10) NOT NULL,
                `name` varchar(255) DEFAULT NULL,
                `description` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `reconciliation_medication` (
                `id` INT(10) AUTO_INCREMENT,
                `reconciliation_id` INT(10) NOT NULL,
                `name` varchar(255) DEFAULT NULL,
                `dose` varchar(255) DEFAULT NULL,
                `route` varchar(255) DEFAULT NULL,
                `frequency` varchar(255) DEFAULT NULL,
                `current` tinyint(4) DEFAULT NULL,
                `pre_op` tinyint(4) DEFAULT NULL,
                `post_op` tinyint(4) DEFAULT NULL,
                `rx` tinyint(4) DEFAULT NULL,
                `verify` tinyint(4) DEFAULT NULL,
                `resume` tinyint(4) DEFAULT NULL,
                `discontinue` tinyint(4) DEFAULT NULL,
                `comments` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
            CREATE TABLE IF NOT EXISTS `reconciliation_visit_update` (
                `id` INT(10) AUTO_INCREMENT,
                `reconciliation_id` INT(10) NOT NULL,
                `no_change` tinyint(1) NULL DEFAULT '0',
                `change_listed` tinyint(1) NULL DEFAULT '0',
                `date` DATE NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
        ");
    }
}
