<?php

use \Console\Migration\BaseMigration;

class UpdatePatientUserTable extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient_user`
            	ADD COLUMN `show_insurance_banner` TINYINT NOT NULL DEFAULT '1';
        ");
    }
}
