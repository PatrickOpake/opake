<?php

use \Console\Migration\BaseMigration;

class RevisedCodingBilling extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `case_coding_bill` (
                `id` INT(11) AUTO_INCREMENT,
                `coding_id` INT(11) NOT NULL,
                `case_type_id` INT(11) NULL DEFAULT NULL,
                `fee_id` INT(11) NULL DEFAULT NULL,
                `charge` FLOAT NULL DEFAULT NULL,
                `amount` FLOAT NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
