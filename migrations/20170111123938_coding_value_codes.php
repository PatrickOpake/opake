<?php

use \Console\Migration\BaseMigration;

class CodingValueCodes extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `value_code` (
                `id` int(11) NOT NULL,
                `code` varchar(22) NULL DEFAULT NULL,
                `description` varchar(255) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `value_code` ADD PRIMARY KEY (`id`);
            ALTER TABLE `value_code` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            CREATE TABLE `case_coding_value` (
                `id` INT(11) AUTO_INCREMENT,
                `coding_id` INT(11) NOT NULL,
                `value_code_id` INT(11) NULL DEFAULT NULL,
                `amount` FLOAT NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
