<?php

use \Console\Migration\BaseMigration;

class PatientUserSessionMissedField extends BaseMigration
{
    public function change()
    {
        try {
            $this->query("
               ALTER TABLE `patient_user_session`
                    ADD COLUMN `active` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_remember_me`;
          ");
        } catch (\Exception $e) {
            $this->writeln("active column already exists, skipped");
        }
    }
}
