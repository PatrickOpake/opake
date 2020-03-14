<?php

use \Console\Migration\BaseMigration;

class InsuranceCaseTypes extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `case_registration_insurance_case_type` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `insurance_id` INT(11) NOT NULL,
                `case_type_id` INT(11) NULL DEFAULT NULL,
                `is_pre_authorization` TINYINT(1) NULL DEFAULT '0',
                `pre_authorization` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
