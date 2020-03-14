<?php

use \Console\Migration\BaseMigration;

class PatientUserTmpPasswordProperty extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient_user`
                ADD COLUMN `is_tmp_password` tinyint(1) NOT NULL DEFAULT 1 AFTER `password`;
        ");
    }
}
