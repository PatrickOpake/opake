<?php

use \Console\Migration\BaseMigration;

class CaseUserOrder extends BaseMigration
{
    public function change()
    {

        $this->query("
            ALTER TABLE `case_user`
                ADD COLUMN `order` SMALLINT NULL DEFAULT '0' AFTER `active`;
        ");

    }
}
