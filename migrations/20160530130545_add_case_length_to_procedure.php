<?php

use \Console\Migration\BaseMigration;

class AddCaseLengthToProcedure extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_type`
	          ADD COLUMN `length` TIME NULL AFTER `name`;
        ");
    }
}
