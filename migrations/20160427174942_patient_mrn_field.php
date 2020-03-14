<?php

use \Console\Migration\BaseMigration;

class PatientMrnField extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient`
                ADD COLUMN `mrn` varchar(60) NULL AFTER `language_id`;

            UPDATE patient SET mrn = id;
        ");
    }
}
