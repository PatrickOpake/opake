<?php

use \Console\Migration\BaseMigration;

class AdmittingDiagnosisEnhancements extends BaseMigration
{
    public function change()
    {
        $this->query("
            DROP TABLE `case_registration_admitting_diagnosis`;
            ALTER TABLE `case_registration` ADD COLUMN `admitting_diagnosis_id` INT(11) NULL DEFAULT NULL AFTER `patients_relations`;
            ALTER TABLE `case_registration` ADD COLUMN `secondary_diagnosis_id` INT(11) NULL DEFAULT NULL AFTER `admitting_diagnosis_id`;
        ");
    }
}
