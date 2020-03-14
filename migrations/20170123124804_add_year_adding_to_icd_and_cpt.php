<?php

use \Console\Migration\BaseMigration;

class AddYearAddingToIcdAndCpt extends BaseMigration
{
    public function change()
    {
        $this->query('
            ALTER TABLE `case_type` ADD COLUMN `year_adding` INT(11) NULL DEFAULT 2016;
            ALTER TABLE `icd` ADD COLUMN `year_adding` INT(11) NULL DEFAULT 2016;
        ');
    }
}
