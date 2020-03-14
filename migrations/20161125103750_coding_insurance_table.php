<?php

use \Console\Migration\BaseMigration;

class CodingInsuranceTable extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `case_coding_insurance` (
                `id` INT(11) AUTO_INCREMENT,
                `coding_id` INT(11) NOT NULL,
                `order_number` INT(11) NULL DEFAULT NULL,
                `prior_payments` FLOAT NULL DEFAULT NULL,
                `adjudicated_claim` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
