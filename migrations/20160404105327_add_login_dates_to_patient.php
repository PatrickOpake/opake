<?php

use \Console\Migration\BaseMigration;

class AddLoginDatesToPatient extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient_user`
                ADD COLUMN `first_login_date` DATETIME NULL DEFAULT NULL AFTER `created`,
                ADD COLUMN `last_login_date` DATETIME NULL DEFAULT NULL AFTER `first_login_date`;
        ");
    }
}
