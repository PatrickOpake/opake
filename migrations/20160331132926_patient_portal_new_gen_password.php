<?php

use \Console\Migration\BaseMigration;

class PatientPortalNewGenPassword extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient_user`
	          ADD COLUMN `new_gen_password` VARCHAR(255) NULL DEFAULT NULL AFTER `password`;
        ");
    }
}
