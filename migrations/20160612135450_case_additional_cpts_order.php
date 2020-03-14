<?php

use \Console\Migration\BaseMigration;

class CaseAdditionalCptsOrder extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_additional_type` ADD `order` TINYINT(3) UNSIGNED NOT NULL;
        ");
    }
}
