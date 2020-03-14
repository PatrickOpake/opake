<?php

use \Console\Migration\BaseMigration;

class TravelOutsideField extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient_appointment_form_pre_operative`
                CHANGE COLUMN `travel_outside` `travel_outside` MEDIUMTEXT NULL DEFAULT NULL AFTER `conditions`;
        ");
    }
}
