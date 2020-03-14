<?php

use \Console\Migration\BaseMigration;

class CodeInputsSectionOnCodingPage extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_coding` ADD `discharge_code_id` INT(11) NULL DEFAULT NULL;
            
            CREATE TABLE `case_coding_occurrence` (
                `id` INT(11) AUTO_INCREMENT,
                `coding_id` INT(11) NOT NULL,
                `occurrence_code_id` INT(11) NULL DEFAULT NULL,
                `date` DATE NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
	        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	        
            CREATE TABLE `coding_condition_code` (
                `coding_id` int(11) NOT NULL,
                `code_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `coding_condition_code`
                ADD UNIQUE KEY `uni` (`coding_id`, `code_id`);
        ");
    }
}
