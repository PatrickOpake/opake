<?php

use \Console\Migration\BaseMigration;

class VerificationStatusToRegistration extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_registration`
                ADD COLUMN `verification_status` TINYINT NULL DEFAULT '0',
                ADD COLUMN `verification_completed_date` DATETIME NULL DEFAULT NULL;
        ");

        $this->query("
            ALTER TABLE `case`
                DROP COLUMN `verification_status`,
                DROP COLUMN `verification_completed_date`;
        ");
    }
}
