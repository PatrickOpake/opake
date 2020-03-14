<?php

use \Console\Migration\BaseMigration;

class InsurancePayers extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `insurance_payor` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL,
                `remote_payor_id` VARCHAR(255) NULL,
                `is_remote_payor` TINYINT NOT NULL DEFAULT '0',
                `actual` TINYINT NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
