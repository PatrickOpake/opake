<?php

use \Console\Migration\BaseMigration;

class AddVerificationFieldsToCase extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case` 
                ADD COLUMN `verification_status` TINYINT NULL DEFAULT '0',
                ADD COLUMN `verification_completed_date` DATETIME NULL DEFAULT NULL;
        ");
    }
}
