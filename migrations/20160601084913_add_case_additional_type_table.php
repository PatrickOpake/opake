<?php

use \Console\Migration\BaseMigration;

class AddCaseAdditionalTypeTable extends BaseMigration
{
    public function change()
    {
        $this->query("       
            CREATE TABLE IF NOT EXISTS `case_additional_type` (
                `type_id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_additional_type` ADD UNIQUE KEY `uni` (`type_id`,`case_id`);
        ");
    }
}
