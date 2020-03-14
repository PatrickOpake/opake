<?php

use \Console\Migration\BaseMigration;

class AddAccompaniedFieldsToCase extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case` 
                ADD `accompanied_phone` VARCHAR(40) NULL DEFAULT NULL,
                ADD `accompanied_email` VARCHAR(255) NULL DEFAULT NULL;
        ");
    }
}
