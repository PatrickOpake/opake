<?php

use \Console\Migration\BaseMigration;

class RevisedCoding extends BaseMigration
{
    public function change()
    {
        $this->query("
            DROP TABLE `case_coding`;
            DROP TABLE `case_coding_admitting_diagnosis`;
            DROP TABLE `case_coding_apc`;
            DROP TABLE `case_coding_drg`;
            DROP TABLE `case_coding_final_diagnose`;
            DROP TABLE `case_coding_note`;
            DROP TABLE `case_coding_occurrence`;
            DROP TABLE `case_coding_procedure`;
            DROP TABLE `case_coding_supply`;

            CREATE TABLE `case_coding` (
                `id` INT(11) AUTO_INCREMENT,
                `case_id` INT(11) NOT NULL,
		`authorization_release_information_payment` tinyint(1) NOT NULL DEFAULT '0',
                `bill_type` tinyint(4) DEFAULT NULL,
                `remarks` TEXT DEFAULT NULL,
                PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            CREATE TABLE `case_coding_diagnosis` (
                `id` INT(11) AUTO_INCREMENT,
                `coding_id` INT(11) NOT NULL,
                `icd_id` INT(11) NOT NULL,
                `row` tinyint(4) NOT NULL,
                PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
